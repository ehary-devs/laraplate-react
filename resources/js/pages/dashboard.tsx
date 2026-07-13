import { Head } from '@inertiajs/react';
import { Bell, KeyRound, ShieldCheck, Users } from 'lucide-react';
import type { LucideIcon } from 'lucide-react';
import { EmptyState } from '@/components/empty-state';
import Heading from '@/components/heading';
import { StatCard } from '@/components/stat-card';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { cn } from '@/lib/utils';
import { dashboard } from '@/routes';

interface Stats {
    users: number;
    roles: number;
    permissions: number;
    unread_notifications: number;
}

type NotificationLevel = 'success' | 'warning' | 'danger' | 'info';

interface RecentNotification {
    id: string;
    title: string;
    message: string;
    level: NotificationLevel | string;
    created_at: string;
}

interface Props {
    stats: Stats;
    recentNotifications: RecentNotification[];
}

const dotColor: Record<string, string> = {
    success: 'bg-green-500',
    warning: 'bg-yellow-500',
    danger: 'bg-red-500',
    info: 'bg-blue-500',
};

const statWidgets: { key: keyof Stats; label: string; icon: LucideIcon }[] = [
    { key: 'users', label: 'Total Users', icon: Users },
    { key: 'roles', label: 'Roles', icon: ShieldCheck },
    { key: 'permissions', label: 'Permissions', icon: KeyRound },
    { key: 'unread_notifications', label: 'Unread Alerts', icon: Bell },
];

export default function Dashboard({ stats, recentNotifications }: Props) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6 px-4 py-6">
                <Heading
                    title="Dashboard"
                    description="Overview of your application."
                />

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {statWidgets.map((widget) => (
                        <StatCard
                            key={widget.key}
                            label={widget.label}
                            value={stats[widget.key]}
                            icon={widget.icon}
                        />
                    ))}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {recentNotifications.length > 0 ? (
                            <div className="divide-y divide-border">
                                {recentNotifications.map((notification) => (
                                    <div
                                        key={notification.id}
                                        className="flex items-start gap-4 py-3 first:pt-0 last:pb-0"
                                    >
                                        <span
                                            className={cn(
                                                'mt-1.5 size-2 shrink-0 rounded-full',
                                                dotColor[notification.level] ??
                                                    dotColor.info,
                                            )}
                                        />
                                        <div className="flex-1">
                                            <p className="text-sm font-medium">
                                                {notification.title}
                                            </p>
                                            <p className="text-sm text-muted-foreground">
                                                {notification.message}
                                            </p>
                                        </div>
                                        <span className="shrink-0 text-xs text-muted-foreground">
                                            {notification.created_at}
                                        </span>
                                    </div>
                                ))}
                            </div>
                        ) : (
                            <EmptyState
                                title="No recent activity"
                                description="Notifications will appear here as they arrive."
                            />
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
