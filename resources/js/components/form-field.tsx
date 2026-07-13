import type { ReactNode } from 'react';
import InputError from '@/components/input-error';
import { Label } from '@/components/ui/label';
import { cn } from '@/lib/utils';

type FormFieldProps = {
    label: string;
    htmlFor?: string;
    error?: string;
    required?: boolean;
    hint?: string;
    className?: string;
    children: ReactNode;
};

/** Label + control + inline error, the standard form row for this app. */
export function FormField({
    label,
    htmlFor,
    error,
    required,
    hint,
    className,
    children,
}: FormFieldProps) {
    return (
        <div className={cn('grid gap-2', className)}>
            <Label htmlFor={htmlFor}>
                {label}
                {required ? (
                    <span className="ml-0.5 text-red-600 dark:text-red-400">
                        *
                    </span>
                ) : null}
            </Label>
            {children}
            {hint ? (
                <p className="text-sm text-muted-foreground">{hint}</p>
            ) : null}
            <InputError message={error} />
        </div>
    );
}
