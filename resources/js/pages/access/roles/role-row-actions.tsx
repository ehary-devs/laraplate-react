import { Link, router } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';
import { useState } from 'react';
import RoleController from '@/actions/Modules/Access/Http/Controllers/RoleController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { useCan } from '@/hooks/use-can';
import type { RoleRow } from '../types';

/** Per-row Edit/Delete menu; entries hidden without the matching permission. */
export function RoleRowActions({ role }: { role: RoleRow }) {
    const { can } = useCan();
    const [confirmOpen, setConfirmOpen] = useState(false);

    const canUpdate = can('role.update');
    const canDelete = can('role.delete');

    if (!canUpdate && !canDelete) {
        return null;
    }

    const destroy = () => {
        router.delete(RoleController.destroy.url({ role: role.id }), {
            preserveScroll: true,
            onFinish: () => setConfirmOpen(false),
        });
    };

    return (
        <>
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <Button variant="ghost" size="icon" className="size-8">
                        <MoreHorizontal className="size-4" />
                        <span className="sr-only">Open menu</span>
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    {canUpdate ? (
                        <DropdownMenuItem asChild>
                            <Link href={RoleController.edit.url({ role: role.id })}>
                                Edit
                            </Link>
                        </DropdownMenuItem>
                    ) : null}
                    {canDelete ? (
                        <DropdownMenuItem
                            variant="destructive"
                            onSelect={(event) => {
                                event.preventDefault();
                                setConfirmOpen(true);
                            }}
                        >
                            Delete
                        </DropdownMenuItem>
                    ) : null}
                </DropdownMenuContent>
            </DropdownMenu>

            <Dialog open={confirmOpen} onOpenChange={setConfirmOpen}>
                <DialogContent>
                    <DialogTitle>Delete {role.name}?</DialogTitle>
                    <DialogDescription>
                        This detaches the role from all users and permissions and
                        deletes it. This cannot be undone.
                    </DialogDescription>
                    <DialogFooter className="gap-2">
                        <DialogClose asChild>
                            <Button variant="secondary">Cancel</Button>
                        </DialogClose>
                        <Button variant="destructive" onClick={destroy}>
                            Delete
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
