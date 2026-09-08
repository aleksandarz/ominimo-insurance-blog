export interface User {
    id: number;
    name: string;
    email?: string;
    role: 'user' | 'admin';
}

export interface Comment {
    id: number;
    comment: string;
    author_name: string;
    author_role: 'user' | 'admin' | 'guest';
    is_guest: boolean;
    can_delete: boolean;
    created_at: string;
}

export interface Post {
    id: number;
    title: string;
    content: string;
    user: User;
    comments: Comment[];
    comments_count?: number;
    can: {
        update: boolean;
        delete: boolean;
    };
    created_at: string;
}

export interface ValidationErrors {
    [key: string]: string[];
}

export interface Paginated<T> {
    data: T[];
    meta: {
        current_page: number;
        last_page: number;
        from: number | null;
        to: number | null;
        total: number;
    };
}