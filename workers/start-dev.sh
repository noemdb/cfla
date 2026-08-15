#!/usr/bin/env bash
#
# Workers de desarrollo local — Cola binnacle + Scheduler
#
# Modo por defecto: el worker de la cola binnacle se ejecuta en PRIMER PLANO
# (ocupa la terminal y sus logs se ven en vivo). El scheduler se lanza en
# background. Sin supervisor ni cron (ambiente local).
#
# Uso:
#   ./workers/start-dev.sh              # worker en primer plano + scheduler bg
#   ./workers/start-dev.sh status       # estado de los procesos
#   ./workers/start-dev.sh stop         # detener scheduler bg (worker se cierra con Ctrl+C)
#   ./workers/start-dev.sh drain        # drenar la cola binnacle (--once)
#
# Notas:
#   - Usa php8.2 explícito (el php del sistema es 7.4, no cumple "^8.2").
#   - Logs scheduler: storage/logs/schedule.log
#   - No usar junto a supervisor (evita doble worker).

set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PHP="${PHP:-/usr/bin/php8.2}"
ARTISAN="$PHP $APP_DIR/artisan"
LOG_DIR="$APP_DIR/storage/logs"
SCHED_PID="$LOG_DIR/schedule-worker.pid"
QUEUE="binnacle"
LOCK="$LOG_DIR/binnacle-worker.lock"

mkdir -p "$LOG_DIR"

is_running() {
    local pid
    pid="$(cat "$1" 2>/dev/null || true)"
    [[ -n "$pid" ]] && kill -0 "$pid" 2>/dev/null
}

status() {
    if is_running "$SCHED_PID"; then
        echo "scheduler:       RUNNING (pid $(cat "$SCHED_PID"))"
    else
        echo "scheduler:       DETENIDO"
    fi

    if pgrep -f "artisan queue:work database --queue=$QUEUE" > /dev/null; then
        echo "worker binnacle: RUNNING (primer plano, pid $(pgrep -f "artisan queue:work database --queue=$QUEUE" | head -1))"
    else
        echo "worker binnacle: DETENIDO"
    fi

    "$PHP" "$APP_DIR/artisan" tinker --execute="echo 'jobs pendientes cola $QUEUE: '.DB::table('jobs')->where('queue','$QUEUE')->count().PHP_EOL;"
}

start_schedule() {
    if is_running "$SCHED_PID"; then
        echo "scheduler ya está corriendo (pid $(cat "$SCHED_PID"))."
        return
    fi

    setsid "$PHP" "$APP_DIR/artisan" schedule:work \
        > "$LOG_DIR/schedule.log" 2>&1 &

    echo $! > "$SCHED_PID"
    sleep 2
    echo "scheduler iniciado (pid $(cat "$SCHED_PID")) — log: $LOG_DIR/schedule.log"
}

stop_schedule() {
    local pid
    pid="$(cat "$SCHED_PID" 2>/dev/null || true)"
    if [[ -n "$pid" ]] && kill -0 "$pid" 2>/dev/null; then
        kill "$pid" 2>/dev/null || true
        sleep 1
        kill -0 "$pid" 2>/dev/null && kill -9 "$pid" 2>/dev/null || true
        echo "scheduler detenido (pid $pid)"
    fi
    rm -f "$SCHED_PID"
}

start_foreground() {
    if pgrep -f "artisan queue:work database --queue=$QUEUE" > /dev/null; then
        echo "worker binnacle ya está corriendo (pid $(pgrep -f "artisan queue:work database --queue=$QUEUE" | head -1))."
        return 1
    fi

    if [[ -f "$LOCK" ]]; then
        echo "Hay un lock pendiente ($LOCK). Si es un cierre previo, bórralo."
        echo "El worker resetea su lock al arrancar; eliminando en 3s..."
        sleep 3
        rm -f "$LOCK"
    fi

    echo "Lanzando worker binnacle en primer plano. Ctrl+C para detener."
    echo "---"
    # exec: el proceso sustituye al script → la terminal queda ocupada por el worker.
    exec "$PHP" "$APP_DIR/artisan" queue:work database \
        --queue="$QUEUE" --sleep=3 --tries=3 --backoff=10
}

drain() {
    "$PHP" "$APP_DIR/artisan" queue:work database \
        --queue="$QUEUE" --once --tries=3 \
        > "$LOG_DIR/binnacle-queue.log" 2>&1
    echo "cola $QUEUE drenada (--once)."
}

case "${1:-start}" in
    start)
        start_schedule
        start_foreground
        ;;
    stop)
        stop_schedule
        if pgrep -f "artisan queue:work database --queue=$QUEUE" > /dev/null; then
            echo "worker binnacle sigue en primer plano — usa Ctrl+C en su terminal."
        else
            echo "worker binnacle: no está corriendo."
        fi
        ;;
    status)
        status
        ;;
    drain)
        drain
        ;;
    *)
        echo "Uso: $0 {start|stop|status|drain}"
        exit 1
        ;;
esac