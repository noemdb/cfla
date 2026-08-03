## 📄 **PROPUESTA DE COTIZACIÓN ADAPTADA PARA EMPRESA**

***

### **COTIZACIÓN No. IA-2026-003 (EMPRESA)**

**Fecha:** 17 de junio de 2026  
**Validez:** 15 días calendario  
**Cliente:** [Nombre de la Empresa]  
**Usuario:** 60 usuarios totales / 20 usuarios activos diarios  
**Requerimiento:** Infraestructura de IA local para empresa con uso continuo máximo de 30 min por usuario

***

## **📊 ANÁLISIS DE CAPACIDAD REQUERIDA**

### **Cálculo de carga real**

| Parámetro | Valor |
|-----------|-------|
| Usuarios totales | 60 |
| Usuarios activos diarios | 20 |
| Uso máximo por usuario | 30 minutos |
| **Usuarios concurrentes estimados** | **8–10** (40–50% de activos) |
| **Horas pico diario** | 2–3 horas (mañana/tarde) |
| **Sesiones totales/día** | 20 sesiones |
| **Tiempo total de uso/día** | 600 minutos (10 horas) |

### **Requisitos técnicos dimensionados**

| Componente | Requerimiento mínimo | Recomendado |
|------------|---------------------|-------------|
| **Modelo base** | 7B–14B (chat rápido) | 32B–70B (coder/agents) |
| **Conexiones simultáneas** | 8–10 | 15–20 (para picos) |
| **Tokens/seg total** | 200–300 tok/s | 400–500 tok/s |
| **RAM mínima** | 32 GB | 64–128 GB |
| **VRAM mínima** | 24 GB | 48–128 GB |
| **Storage** | 1 TB | 2–4 TB |

***

## **🎯 RECOMENDACIÓN PARA TU CASO**

### **✅ OPCIÓN RECOMENDADA: NVIDIA DGX Spark + Servidor Web**

**Razones:**
- **128 GB memoria** → suficiente para 8–10 usuarios concurrentes con modelo 70B
- **27–28 tok/s** × 10 usuarios = **270–280 tok/s total** (suficiente para uso empresarial)
- **Pre-fill rápido (6.7 min)** → mínimo tiempo de espera en sesiones largas
- **Linux nativo** → compatible con Docker, Kubernetes, vLLM para múltiples usuarios
- **240W consumo** → eficiente para operación continua

***

## **📋 ARQUITECTURA SOLUCIÓN EMPRESA**

### **Esquema de implementación**

```
┌─────────────────────────────────────────────────────────┐
│                    EMPRESA (Oficina)                     │
│                                                         │
│  ┌─────────────┐    ┌─────────────────────────────┐   │
│  │   Firewall  │───▶│  Server Web (Nginx/Apache)  │   │
│  │   Red IMA   │    │  Port 80/443                │   │
│  └─────────────┘    └──────────────┬──────────────┘   │
│                                     │                   │
│                              ┌──────▼──────┐           │
│                              │  vLLM/API   │           │
│                              │  Gateway    │           │
│                              │  (8–10 conn)│           │
│                              └──────┬──────┘           │
│                                     │                   │
│                              ┌──────▼──────┐           │
│                              │ NVIDIA DGX  │           │
│                              │ Spark       │           │
│                              │ 128 GB      │           │
│                              │ 1 PFLOP FP4 │           │
│                              └─────────────┘           │
│                                                         │
│  Usuarios: 20 activos/día → 8–10 concurrentes en pico  │
└─────────────────────────────────────────────────────────┘
```

### **Software necesario**

| Componente | Función | Costo |
|------------|---------|-------|
| **vLLM** | API Gateway para múltiples usuarios | Free |
| **Ollama** | Motor de inferencia local | Free |
| **Nginx** | Server web + load balancer | Free |
| **Docker** | Contenedores para escalabilidad | Free |
| **LM Studio** | Interfaz GUI para testing | Free |
| **Monitorización** | Prometheus + Grafana | Free |

***

## **💰 COSTOS ADAPTADOS PARA EMPRESA**

### **OPA 1: NVIDIA DGX Spark (RECOMENDADA)**

| Ítem | Descripción | Precio Unit. | Cantidad | Total |
|------|-------------|--------------|----------|-------|
| 001 | NVIDIA DGX Spark Founders Edition | $4,800 | 1 | $4,800 |
| 002 | Curso IA incluido (€90 valor) | $0 | 1 | $0 |
| 003 | Servior Web Nginx + configuración | $150 | 1 | $150 |
| 004 | vLLM API Gateway + configuración | $200 | 1 | $200 |
| 005 | Docker + Kubernetes (escalabilidad) | $150 | 1 | $150 |
| 006 | Ollama + LM Studio instalado | $100 | 1 | $100 |
| 007 | Monitorización Prometheus/Grafana | $100 | 1 | $100 |
| 008 | Importación USA → VE | $350 | 1 | $350 |
| 009 | Documentación técnica empresarial | $100 | 1 | $100 |
| 010 | Garantía 24 meses internacional | $100 | 1 | $100 |
| 011 | Soporte remoto 60 días (empresa) | $150 | 1 | $150 |
| 012 | Backup diario + disaster recovery | $100 | 1 | $100 |
| | **SUBTOTAL** | | | **$5,750** |
| | Impuesto (16% IVA) | | | $920 |
| | **TOTAL** | | | **$6,670** |

***

### **OPA 2: PC con RTX 4090 (ALTERNATIVA BUDGET)**

| Ítem | Descripción | Precio Unit. | Cantidad | Total |
|------|-------------|--------------|----------|-------|
| 001 | GPU NVIDIA RTX 4090 24GB | $1,850 | 1 | $1,850 |
| 002 | CPU AMD Ryzen 9 7950X3D | $650 | 1 | $650 |
| 003 | RAM 64 GB DDR5 6000 MHz | $280 | 1 | $280 |
| 004 | SSD 2 TB NVMe Gen4 | $180 | 1 | $180 |
| 005 | Motherboard ASUS ROG X670E | $520 | 1 | $520 |
| 006 | PSU 1000W 80+ Gold | $190 | 1 | $190 |
| 007 | Gabinete + Refrigeración | $620 | 1 | $620 |
| 008 | Servior Web + vLLM | $300 | 1 | $300 |
| 009 | Configuración empresarial | $200 | 1 | $200 |
| 010 | Garantía 24 meses | $120 | 1 | $120 |
| 011 | Soporte remoto 60 días | $150 | 1 | $150 |
| | **SUBTOTAL** | | | **$5,060** |
| | Impuesto (16% IVA) | | | $809.60 |
| | **TOTAL** | | | **$5,869.60** |

**⚠️ Limitación RTX 4090:** 24 GB VRAM → solo modelo 7B–14B para 8–10 usuarios concurrentes

***

### **OPA 3: 2× NVIDIA DGX Spark (SCALABILITY)**

Si necesitas **20 usuarios concurrentes** (pico máximo):

| Ítem | Descripción | Precio Unit. | Cantidad | Total |
|------|-------------|--------------|----------|-------|
| 001 | NVIDIA DGX Spark Founders Edition | $4,800 | 2 | $9,600 |
| 002 | Load Balancer + vLLM multi-node | $300 | 1 | $300 |
| 003 | Configuración cluster | $300 | 1 | $300 |
| 004 | Importación + instalación | $500 | 1 | $500 |
| 005 | Soporte empresarial 90 días | $250 | 1 | $250 |
| | **SUBTOTAL** | | | **$10,950** |
| | Impuesto (16% IVA) | | | $1,752 |
| | **TOTAL** | | | **$12,702** |

**Capacidad:** 20 usuarios concurrentes con modelo 70B → **55–56 tok/s total**

***

## **📊 COMPARATIVA PARA TU CASO ESPECÍFICO**

| Característica | DGX Spark (1) | RTX 4090 | DGX Spark (2) |
|----------------|---------------|----------|---------------|
| **Precio total** | **$6,670** | **$5,870** | $12,702 |
| **Usuarios concurrentes** | 8–10 ✅ | 8–10 (solo 7B–14B) | 20 ✅ |
| **Modelo recomendado** | 32B–70B | 7B–14B | 70B |
| **Tokens/seg total** | 270–280 | 200–250 | 550–560 |
| **Tiempo respuesta** | 3–4 sec | 2–3 sec | 2–3 sec |
| **Escalabilidad** | ✓ (2×DGX) | ✗ | ✓+ |
| **Ideal para** | 20 usuarios/día | 20 usuarios/día | 40–50 usuarios/día |

***

## **💡 RECOMENDACIÓN FINAL PARA TU EMPRESA**

### **✅ Opción recomendada: NVIDIA DGX Spark (1 equipo)**

**Razones:**
1. **20 usuarios/día** × 30 min = 600 min total → **8–10 concurrentes en pico** (suficiente con 1 DGX)
2. **Modelo 32B–70B** → calidad suficiente para coder, chat empresarial, agents
3. **270–280 tok/s total** → 27–28 tok/s por usuario (aceptable para uso empresarial)
4. **Precio $6,670** → ROI en 2 años vs. APIs cloud ($100/mes = $3,600/3 años)
5. **Linux + Docker** → escalable a 2×DGX cuando necesites más capacidad

### **⚠️ Si priorizas BUDGET (precio bajo):**
**→ PC con RTX 4090 ($5,870)**  
- Solo modelos 7B–14B (menos calidad)
- 200–250 tok/s total (más lento)
- No escalable

### **⚠️ Si necesitas 20 concurrentes garantizados:**
**→ 2× NVIDIA DGX Spark ($12,702)**  
- 550–560 tok/s total
- Modelo 70B para todos
- Escalable a 4×DGX

***

## **📋 PLAN DE IMPLEMENTACIÓN (30 días)**

| Semana | Actividad |
|--------|-----------|
| **1** | Compra DGX Spark + importación USA |
| **2** | Llegada a VE + instalación física |
| **3** | Configuración vLLM + Docker + Nginx |
| **4** | Testing con 5–10 usuarios + capacitación |
| **5** | Lanzamiento oficial 20 usuarios |

***

## **🛠️ SOPORTE INCLUIDO**

- ✅ Configuración inicial completa (vLLM, Docker, Nginx)
- ✅ 60 días de soporte remoto empresarial
- ✅ Documentación técnica en español
- ✅ Backup diario + disaster recovery
- ✅ Monitorización Prometheus/Grafana
- ✅ Capacitación para 3 administradores

***

_______
Firma y autorización

_______
Nombre y cargo

_______
Fecha

***