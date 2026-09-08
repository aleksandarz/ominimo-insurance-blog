import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { Paginated, Post } from '../types';
import { takeFlash } from '../flash';
import FlashMessage from './FlashMessage';
import Pagination from './Pagination';

export default function PostList() {
    const [posts, setPosts] = useState<Post[]>([]);
    const [page, setPage] = useState(1);
    const [meta, setMeta] = useState<Paginated<Post>['meta'] | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [notice, setNotice] = useState<string | null>(null);

    useEffect(() => {
        setNotice(takeFlash());
    }, []);

    useEffect(() => {
        setLoading(true);
        axios
            .get<Paginated<Post>>('/api/posts', { params: { page } })
            .then((res) => {
                setPosts(res.data.data);
                setMeta(res.data.meta);
                setError(null);
            })
            .catch(() => setError('Could not load posts. Please try again.'))
            .finally(() => setLoading(false));
    }, [page]);

    const goToPage = (target: number) => {
        setPage(target);
        window.scrollTo({ top: 0 });
    };

    return (
        <div className="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {notice && <FlashMessage>{notice}</FlashMessage>}

            {loading && <p className="text-gray-500">Loading...</p>}
            {error && <p className="text-red-600">{error}</p>}

            {!loading && !error && posts.length === 0 && <p className="text-gray-500">No posts yet.</p>}

            {!loading &&
                !error &&
                posts.map((post) => (
                    <div key={post.id} className="bg-white p-6 shadow-sm sm:rounded-lg">
                        <Link to={`/posts/${post.id}`} className="text-lg font-semibold hover:underline">
                            {post.title}
                        </Link>
                        <p className="text-sm text-gray-500 mt-1">
                            by {post.user.name} · {post.created_at}
                        </p>
                        <p className="text-gray-700 mt-3">
                            {post.content.length > 150 ? `${post.content.slice(0, 150).trimEnd()}...` : post.content}
                        </p>
                    </div>
                ))}

            {!loading && !error && meta && (
                <Pagination
                    currentPage={meta.current_page}
                    lastPage={meta.last_page}
                    from={meta.from}
                    to={meta.to}
                    total={meta.total}
                    onChange={goToPage}
                />
            )}
        </div>
    );
}
