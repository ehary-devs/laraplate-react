import type { LucideIcon } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { cn } from '@/lib/utils';

type StatCardProps = {
    label: string;
    value: number | string;
    icon: LucideIcon;
    className?: string;
};

/** Compact metric widget: label, value, and an icon badge. */
export function StatCard({ label, value, icon: Icon, className }: StatCardProps) {
    return (
        <Card className={cn('py-4', className)}>
            <CardContent className="flex items-center justify-between gap-4 px-4">
                <div className="flex flex-col gap-1">
                    <span className="text-sm text-muted-foreground">{label}</span>
                    <span className="text-2xl font-semibold tabular-nums">
                        {value}
                    </span>
                </div>
                <div className="rounded-full bg-muted p-3 text-muted-foreground">
                    <Icon className="size-5" />
                </div>
            </CardContent>
        </Card>
    );
}
