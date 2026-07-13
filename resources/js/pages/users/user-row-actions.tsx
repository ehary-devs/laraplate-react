import { Link, router } from '@inertiajs/react';
import { MoreHorizontal } from 'lucide-react';
import { useState } from 'react';
import UserController from '@/actions/Modules/User/Http/Controllers/UserController';
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
import type { UserRow } from './types';

/** Per-row Edit/Delete menu; entries are hidden without the matching permission. */
export function UserRowActions({ user }: { user: UserRow }) {
    const { can } = useCan();
    const [confirmOpen, setConfirmOpen] = useState(false);

    const canUpdate = can('user.update');
    const canDelete = can('user.delete');

    if (!canUpdate && !canDelete) {
        return null;
    }

    const destroy = () => {
        router.delete(UserController.destroy.url({ user: user.id }), {
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
                            <Link href={UserController.edit.url({ user: user.id })}>
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
                    <DialogTitle>Delete {user.name}?</DialogTitle>
                    <DialogDescription>
                        This soft-deletes the user. This action can be reversed by
                        an administrator.
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
