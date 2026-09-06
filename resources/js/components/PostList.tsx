import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import axios from 'axios';
import { Post } from '../types';

export default function PostList() {
    const [posts, setPosts] = useState<Post[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get('/api/posts').then((res) => {
            setPosts(res.data.data);
            setLoading(false);
        });
    }, []);

    if (loading) return <p className="text-gray-500">Loading...</p>;

    return (
        <div className="space-y-4">
            {posts.map((post) => (
                <div key={post.id} className="bg-white p-6 shadow-sm rounded-lg">
                    <Link to={`/posts/${post.id}`} className="text-lg font-semibold hover:underline">
                        {post.title}
                    </Link>
                    <p className="text-sm text-gray-500 mt-1">
                        by {post.user.name} · {post.created_at}
                    </p>
                </div>
            ))}
        </div>
    );
}