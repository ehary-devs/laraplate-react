import '@tanstack/react-table';

declare module '@tanstack/react-table' {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    interface ColumnMeta<TData extends RowData, TValue> {
        /** Column header label rendered by the DataTable. */
        title?: string;
        /** Backend sort key; presence makes the column sortable. */
        sortKey?: string;
        /** Extra className applied to the header cell. */
        headerClassName?: string;
    }
}
