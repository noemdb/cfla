<?php

namespace App\Models\app\Estudiante\Functions\Representants;

use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Planpago\RegistroPagoCombinado;
use App\Models\app\Pescolar;
use App\Models\app\Estudiante\Abono;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\CreditoAplicado;
use Carbon\Carbon;

trait Auditoria //Reporte Auditoria 
{

	public function getExchangeExpireBillsByName($name)
	{
		$cuentaxpagars = collect();
		foreach ($this->estudiants as $estudiant) {
			$bills = $estudiant->exchange_expire_bills->where('name', $name);
			if ($bills->count() > 0) {
				$cuentaxpagars = $cuentaxpagars->merge($bills);
			}
		}
		return $cuentaxpagars;
	}

	public function getPagosByQuota($mes)
	{
		$query = Pago::select('pagos.*')
			->join('ingresos', 'ingresos.id', '=', 'pagos.ingreso_id')
			->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
			->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
			->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
			->join('estudiants', 'estudiants.id', '=', 'registro_pagos.estudiant_id')
			->where('cuentaxpagars.name', 'like', '%' . $mes . '%')
			->where(function ($query) {
				$query->where('cuentaxpagars.type', 'GENERAL')
					->orWhere(function ($q) {
						$q->where('cuentaxpagars.type', 'INDIVIDUAL')
							->whereColumn('cuentaxpagars.estudiant_id', 'estudiants.id');
					});
			})
			->where('registro_pagos.representant_id', $this->id)
			->groupBy('pagos.id');
		$pagos = $query->get();
		return $pagos;
	}

	public function getAbonosAplicadosByMes($mes)
	{
		[$start, $end] = $this->getDateRangeForMes($mes);

		if (!$start || !$end) {
			return collect();
		}

		return Abono::select('abonos.*', 'ingresos.exchange_ammount as exchange_ammount_applied')
			->withTrashed()
			->join('abono_aplicados', 'abonos.id', '=', 'abono_aplicados.abono_id')
			->join('registro_pagos', 'registro_pagos.id', '=', 'abono_aplicados.registro_pago_id')
			->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
			->join('ingresos', 'ingresos.id', '=', 'abonos.ingreso_id')
			->where('cuentaxpagars.name', $mes)
			->where('abonos.representant_id', $this->id)
			->whereDate('ingresos.date_payment', '>=', $start->format('Y-m-d'))
			->whereDate('ingresos.date_payment', '<=', $end->format('Y-m-d'))
			->orderBy('ingresos.date_payment', 'asc')
			->get();
	}

	public function getCreditoAplicadosByMes($mes)
	{
		[$start, $end] = $this->getDateRangeForMes($mes);

		if (!$start || !$end) {
			return collect();
		}

		$query = CreditoAFavor::select('credito_a_favors.*')
			->withTrashed()
			->join('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
			->join('registro_pagos', 'registro_pagos.id', '=', 'credito_aplicados.registro_pago_id')
			->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')
			//->where('cuentaxpagars.name', 'like', $mes . '%')
			->where('cuentaxpagars.name', $mes)
			->where('credito_a_favors.representant_id', $this->id)
			->orderBy('credito_a_favors.created_at', 'asc')
			->get();
		return $query;
	}

	public function getCreditosGeneradosByMes($mes)
	{
		// Obtener IDs de RegistroPago para este mes y representante
		$reg_pag_ids = RegistroPago::join('cuentaxpagars', 'registro_pagos.cuentaxpagar_id', '=', 'cuentaxpagars.id')
			->where('cuentaxpagars.name', 'like', '%' . $mes . '%')
			->where('registro_pagos.representant_id', $this->id)
			->pluck('registro_pagos.id')
			->toArray();
		if ($mes == 'SEPTIEMBRE') {
			//dd($reg_pag_ids);
		}

		// IDs de CreditoAFavor aplicados en estos pagos
		$ca_ids = CreditoAplicado::whereIn('registro_pago_id', $reg_pag_ids)
			->pluck('credito_a_favor_id')
			->toArray();

		// CreditoAFavor generados en estos pagos pero no aplicados en ellos
		$credito_a_favors = CreditoAFavor::withTrashed()
			->whereIn('registro_pago_id', $reg_pag_ids)
			->whereNotIn('id', $ca_ids)
			->get();
		if ($mes == 'OCTUBRE') {
			//dd($credito_a_favors);
		}
		return $credito_a_favors;
	}

	public function getIngresosByMes($mes)
	{
		[$finicial, $ffinal] = $this->getDateRangeForMes($mes);

		// Si no hay rango (cuotas especiales mal mapeadas, etc.)
		$filterByDate = !is_null($finicial) && !is_null($ffinal);

		$query = Ingreso::select('ingresos.*')
			->join('pagos', 'ingresos.id', '=', 'pagos.ingreso_id')
			->join('bancos', 'bancos.id', '=', 'ingresos.banco_id')
			->join('registro_pagos', 'registro_pagos.id', '=', 'pagos.registro_pago_id')
			->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'registro_pagos.cuentaxpagar_id')

			->where('bancos.is_adjustment', 'false')
			->where('cuentaxpagars.name', $mes)

			->where('ingresos.representant_id', $this->id)
			->groupBy('ingresos.id');

		// Ejecutar consulta y obtener modelos Ingreso
		$ingresos = $query->get();

		// Solo ingresos válidos: aquellos que tienen factura asociada a la cuota ($mes)
		$ingresos = $ingresos->filter(function ($ingreso) use ($mes) {
			return !is_null($ingreso->invoiceNumberByQuota($mes));
		});

		// Opcional: reindexar claves
		return $ingresos->values();
	}

	public function getConceptoPagosByMes($mes)
	{
		$conceptos = collect();
		$mes_normalizado = $this->normalizeName($mes);
		foreach ($this->estudiants as $estudiant) {
			$planpago = $estudiant->planpago;
			if ($planpago) {
				$cuentaxpagars = $planpago->cuentaxpagars->filter(function ($item) use ($mes_normalizado) {
					return $this->normalizeName($item->name) === $mes_normalizado;
				});
				foreach ($cuentaxpagars as $cuentaxpagar) {
					foreach ($cuentaxpagar->conceptopagos as $conceptopago) {
						$conceptos->push($conceptopago);
					}
				}
			}
		}
		return $conceptos;
	}

	public function TotalExchangeMontoCuentasXPagarNeto($mes)
	{
		return ConceptoPago::join('cuentaxpagars', 'concepto_pagos.cuentaxpagar_id', '=', 'cuentaxpagars.id')
			->join('planpagos', 'cuentaxpagars.planpago_id', '=', 'planpagos.id')
			->join('administrativas', 'administrativas.planpago_id', '=', 'planpagos.id')
			->join('estudiants', 'administrativas.estudiant_id', '=', 'estudiants.id')
			->where('estudiants.representant_id', $this->id)
			->where('cuentaxpagars.name', 'like', '%' . $mes . '%')
			->where(function ($query) {
				$query->where('cuentaxpagars.type', 'GENERAL')
					->orWhere(function ($q) {
						$q->where('cuentaxpagars.type', 'INDIVIDUAL')
							->whereColumn('cuentaxpagars.estudiant_id', 'estudiants.id');
					});
			})
			->whereNull('concepto_pagos.deleted_at')
			->whereNull('cuentaxpagars.deleted_at')
			->whereNull('estudiants.deleted_at')
			->sum('concepto_pagos.exchange_ammount');
	}

	private function getDateRangeForMes($mes)
	{
		// Normalizar mes
		$original = $mes;
		$mes = mb_strtoupper(trim($mes), 'UTF-8');
		$mes = str_replace(
			['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
			['A', 'E', 'I', 'O', 'U', 'N'],
			$mes
		);

		// Casos especiales que deben tratarse como SEPTIEMBRE
		if (in_array($mes, ['INSCRIPCION', 'MATRICULA'])) {
			$mes = 'SEPTIEMBRE';
		}

		$pescolar = Pescolar::active()->first();
		if (!$pescolar) {
			return [null, null];
		}

		// Año de inicio (ej. 2025)
		$yearStart = Carbon::parse($pescolar->finicial)->year;
		// Año de cierre (ej. 2026)
		$yearEnd = Carbon::parse($pescolar->ffinal)->year;

		// Mapa meses → número
		$mapMeses = [
			'SEPTIEMBRE' => 9,
			'OCTUBRE'    => 10,
			'NOVIEMBRE'  => 11,
			'DICIEMBRE'  => 12,
			'ENERO'      => 1,
			'FEBRERO'    => 2,
			'MARZO'      => 3,
			'ABRIL'      => 4,
			'MAYO'       => 5,
			'JUNIO'      => 6,
			'JULIO'      => 7,
			'AGOSTO'     => 8,
		];

		if (!isset($mapMeses[$mes])) {
			return [null, null];
		}

		$monthNum = $mapMeses[$mes];
		$year = $monthNum >= 9 ? $yearStart : $yearEnd;

		// Crear rango
		$start = Carbon::create($year, $monthNum, 1)->startOfMonth();
		$end   = Carbon::create($year, $monthNum, 1)->endOfMonth();

		return [$start, $end];
	}

	private function normalizeName($value)
	{
		if (!$value) return null;

		// Quitar espacios
		$value = trim($value);

		// Convertir a mayúsculas
		$value = mb_strtoupper($value, 'UTF-8');

		// Remover tildes
		$value = str_replace(
			['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
			['A', 'E', 'I', 'O', 'U', 'N'],
			$value
		);

		return $value;
	}
}
