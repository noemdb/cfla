<?php

namespace App\Contracts;

interface Auditable
{
    /**
     * Allowlist de campos permitidos en old_values/new_values.
     * Nunca usar getAttributes() directo — evita fugar password,
     * remember_token, o cualquier campo no revisado explícitamente.
     */
    public function auditableAttributes(): array;

    /**
     * Campos dentro de auditableAttributes() que deben enmascararse
     * (ej: email -> "j***z@***.com", cédula, número de referencia)
     * en vez de guardarse en claro.
     */
    public function maskedAuditFields(): array;
}
