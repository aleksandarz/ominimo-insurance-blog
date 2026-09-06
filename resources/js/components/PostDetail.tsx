import { useEffect, useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import CommentList from './CommentList';
import { Post, Comment, User } from '../types';

interface Props {
    currentUser: User | null;
}

export default function PostDetail({ currentUser }: Props) {
    const { id } = useParams();
    const navigate = useNavigate();
    const [post, setPost] = useState<Post | null>(null);

    useEffect(() => {
        axios.get(`/api/posts/${id}`).then((res) => setPost(res.data.data));
    }, [id]);

    if (!post) return <p className="text-gray-500">Loading...</p>;

    const handleCommentAdded = (newComment: Comment | null, deletedId?: number) => {
        if (deletedId) {
            setPost({ ...post, comments: post.comments.filter((c) => c.id !== deletedId) });
        } else if (newComment) {
            setPost({ ...post, comments: [newComment, ...post.comments] });
        }
    };

    const deletePost = async () => {
        if (!confirm('Are you sure?')) return;
        await axios.delete(`/api/posts/${post.id}`);
        navigate('/');
    };

    return (
        <div className="space-y-6">
            <div className="bg-white p-6 shadow-sm rounded-lg">
                <h2 className="text-xl font-semibold">{post.title}</h2>
                <p className="text-sm text-gray-500 mt-1 flex items-center gap-2">
                    by {post.user.name}
                    {post.user.role === 'admin' && (
                        <span className="bg-indigo-100 text-indigo-700 text-xs px-2 py-0.5 rounded-full font-medium">
                            Admin
                        </span>
                    )}
                    · {post.created_at}
                </p>
                <div className="mt-4 whitespace-pre-line">{post.content}</div>

                {post.can.update && (
                    <div className="mt-6 flex space-x-3">
                        <Link to={`/posts/${post.id}/edit`} className="text-sm text-indigo-600 hover:underline">
                            Edit
                        </Link>
                        <button onClick={deletePost} className="text-sm text-red-600 hover:underline">
                            Delete
                        </button>
                    </div>
                )}
            </div>

            <CommentList post={post} onCommentAdded={handleCommentAdded} currentUser={currentUser} />
        </div>
    );
}