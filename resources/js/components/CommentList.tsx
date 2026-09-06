import { useState } from 'react';
import axios from 'axios';
import { Post, Comment, User, ValidationErrors } from '../types';

interface Props {
    post: Post;
    onCommentAdded: (newComment: Comment | null, deletedId?: number) => void;
    currentUser: User | null;
}

export default function CommentList({ post, onCommentAdded, currentUser }: Props) {
    const [comment, setComment] = useState('');
    const [guestName, setGuestName] = useState('');
    const [errors, setErrors] = useState<ValidationErrors>({});

    const submitComment = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        try {
            const res = await axios.post(`/api/posts/${post.id}/comments`, {
                comment,
                guest_name: guestName,
            });
            onCommentAdded(res.data.data);
            setComment('');
            setGuestName('');
        } catch (err: any) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors);
            }
        }
    };

    const deleteComment = async (commentId: number) => {
        if (!confirm('Are you sure?')) return;
        await axios.delete(`/api/comments/${commentId}`);
        onCommentAdded(null, commentId);
    };

    return (
        <div className="bg-white p-6 shadow-sm rounded-lg mt-6">
            <h3 className="font-semibold text-lg mb-4">
                Comments ({post.comments.length})
            </h3>

            <div className="space-y-4">
                {post.comments.map((c) => (
                    <div key={c.id} className="border-b pb-3">
                        <div className="flex justify-between items-start">
                            <p className="text-sm font-semibold text-gray-700">
                                {c.author_name} {c.is_guest && '(guest)'}
                            </p>
                            {c.can_delete && (
                                <button
                                    onClick={() => deleteComment(c.id)}
                                    className="text-xs text-red-600 hover:underline"
                                >
                                    Delete
                                </button>
                            )}
                        </div>
                        <p className="text-gray-600 mt-1">{c.comment}</p>
                    </div>
                ))}
            </div>

            <form onSubmit={submitComment} className="mt-6 space-y-4">
                {!currentUser && (
                    <div>
                        <input
                            type="text"
                            placeholder="Your name"
                            value={guestName}
                            onChange={(e) => setGuestName(e.target.value)}
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        />
                        {errors.guest_name && (
                            <p className="text-red-600 text-sm mt-1">{errors.guest_name[0]}</p>
                        )}
                    </div>
                )}

                <div>
                    <textarea
                        placeholder="Write a comment..."
                        value={comment}
                        onChange={(e) => setComment(e.target.value)}
                        rows={3}
                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                        required
                    />
                    {errors.comment && (
                        <p className="text-red-600 text-sm mt-1">{errors.comment[0]}</p>
                    )}
                </div>

                <button type="submit" className="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">
                    Add Comment
                </button>
            </form>
        </div>
    );
}