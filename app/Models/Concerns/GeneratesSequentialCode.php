<?php

namespace App\Models\Concerns;

/**
 * Generates a sequential, collision-safe code for models that expose a
 * human-readable identifier column (e.g. `CLI-202610-001`).
 *
 * The sequence is scoped by the prefix returned from {@see sequentialCodePrefix()},
 * so a monthly prefix such as `CLI-202610-` restarts at 001 each month. Soft
 * deleted rows are taken into account, so a deleted record never has its code
 * handed out twice.
 */
trait GeneratesSequentialCode
{
    protected static function bootGeneratesSequentialCode(): void
    {
        static::creating(function ($model): void {
            $column = $model->sequentialCodeColumn();

            if (! empty($model->{$column})) {
                return;
            }

            $model->{$column} = $model->generateSequentialCode(
                $model->sequentialCodePrefix(),
                $model->sequentialCodePadding(),
            );
        });
    }

    /**
     * Column that stores the generated code.
     */
    abstract public function sequentialCodeColumn(): string;

    /**
     * Prefix the sequence is scoped by, including the trailing separator.
     */
    abstract public function sequentialCodePrefix(): string;

    /**
     * Number of digits the running sequence is padded to.
     */
    public function sequentialCodePadding(): int
    {
        return 3;
    }

    /**
     * Build the next available code for the given prefix.
     */
    public function generateSequentialCode(?string $prefix = null, ?int $padding = null): string
    {
        $prefix ??= $this->sequentialCodePrefix();
        $padding ??= $this->sequentialCodePadding();

        $next = $this->highestSequentialNumber($prefix) + 1;

        do {
            $code = $prefix.str_pad((string) $next, $padding, '0', STR_PAD_LEFT);
            $next++;
        } while ($this->sequentialCodeExists($code));

        return $code;
    }

    /**
     * Highest sequence number already issued for the prefix, soft deleted rows included.
     */
    protected function highestSequentialNumber(string $prefix): int
    {
        $column = $this->sequentialCodeColumn();
        $highest = 0;

        $this->newSequentialCodeQuery()
            ->where($column, 'like', $prefix.'%')
            ->pluck($column)
            ->each(function (?string $code) use ($prefix, &$highest): void {
                if ($code !== null && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $code, $matches)) {
                    $highest = max($highest, (int) $matches[1]);
                }
            });

        return $highest;
    }

    /**
     * Determine whether the given code is already taken, soft deleted rows included.
     */
    protected function sequentialCodeExists(string $code): bool
    {
        return $this->newSequentialCodeQuery()
            ->where($this->sequentialCodeColumn(), $code)
            ->exists();
    }

    /**
     * Build a query that includes soft-deleted rows when the model uses SoftDeletes.
     */
    protected function newSequentialCodeQuery()
    {
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class), true)) {
            return static::withTrashed();
        }

        return static::query();
    }
}
