export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
    role: 'admin' | 'mitra';
    profile_picture?: string;
}

export interface PksSubmission {
    id: number;
    user_id: number;
    title: string;
    description: string;
    document_path?: string;
    status: 'proses' | 'ditolak' | 'disetujui'; // Removed 'revisi' and 'Pembahasan'
    final_document_path?: string;
    digital_signature?: string;
    signed_by?: string;
    signed_at?: string;
    partner_name?: string;
    phone?: string;
    kak_document_path?: string;
    mou_document_path?: string;
    validity_period_start?: string;
    validity_period_end?: string;
    created_at: string;
    updated_at: string;
    user: User;
}

export interface PaginationLinks {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface PaginatedSubmissions {
    data: PksSubmission[];
    links: PaginationLinks;
    meta: PaginationMeta;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
};