import { useEffect, useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import CommentList from './CommentList';
import FlashMessage from './FlashMessage';
import BackToTop from './BackToTop';
import { Post, Comment, User } from '../types';
import { flash, takeFlash } from '../flash';

interface Props {
    currentUser: User | null;
}

export default function PostDetail({ currentUser }: Props) {
    const { id } = useParams();
    const navigate = useNavigate();
    const [post, setPost] = useState<Post | null>(null);
    const [notice, setNotice] = useState<string | null>(null);

    useEffect(() => {
        setNotice(takeFlash());
    }, []);

    useEffect(() => {
        axios.get(`/api/posts/${id}`).then((res) => setPost(res.data.data));
    }, [id]);

    if (!post) return <p className="text-gray-500">Loading...</p>;

    const handleCommentAdded = (newComment: Comment | null, deletedId?: number) => {
        if (deletedId) {
            setPost({ ...post, comments: post.comments.filter((c) => c.id !== deletedId) });
            setNotice('Comment deleted.');
        } else if (newComment) {
            setPost({ ...post, comments: [newComment, ...post.comments] });
            setNotice('Comment added.');
        }
    };

    const deletePost = async () => {
        if (!confirm('Are you sure?')) return;
        try {
            await axios.delete(`/api/posts/${post.id}`);
            flash('Post deleted successfully.');
            navigate('/');
        } catch {
            alert('Could not delete the post.');
        }
    };

    return (
        <div className="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <button
                type="button"
                onClick={() => navigate(-1)}
                className="inline-flex items-center text-sm text-gray-600 hover:text-gray-900"
            >
                ← Back
            </button>

            {notice && <FlashMessage>{notice}</FlashMessage>}

            <div className="bg-white p-6 shadow-sm sm:rounded-lg">
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

            <BackToTop />
        </div>
    );
}
