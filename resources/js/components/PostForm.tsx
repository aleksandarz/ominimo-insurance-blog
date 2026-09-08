import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axios from 'axios';
import { ValidationErrors } from '../types';
import { flash } from '../flash';

export default function PostForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEditing = Boolean(id);

    const [title, setTitle] = useState('');
    const [content, setContent] = useState('');
    const [errors, setErrors] = useState<ValidationErrors>({});
    const [formError, setFormError] = useState<string | null>(null);

    useEffect(() => {
        if (isEditing) {
            axios.get(`/api/posts/${id}`).then((res) => {
                if (!res.data.data.can.update) {
                    navigate(`/posts/${id}`, { replace: true });
                    return;
                }
                setTitle(res.data.data.title);
                setContent(res.data.data.content);
            });
        }
    }, [id]);

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});
        setFormError(null);

        try {
            if (isEditing) {
                await axios.put(`/api/posts/${id}`, { title, content });
                flash('Post updated successfully.');
                navigate(`/posts/${id}`);
            } else {
                await axios.post('/api/posts', { title, content });
                flash('Post created successfully.');
                navigate('/');
            }
        } catch (err: any) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors);
            } else {
                setFormError('Something went wrong. Please try again.');
            }
        }
    };

    return (
        <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <button
                type="button"
                onClick={() => navigate(-1)}
                className="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-4"
            >
                ← Back
            </button>

            <div className="bg-white p-6 shadow-sm sm:rounded-lg">
                {formError && <p className="text-red-600 text-sm mb-4">{formError}</p>}

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium">Title</label>
                        <input
                            type="text"
                            value={title}
                            onChange={(e) => setTitle(e.target.value)}
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                            autoFocus
                        />
                        {errors.title && <p className="text-red-600 text-sm mt-1">{errors.title[0]}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium">Content</label>
                        <textarea
                            value={content}
                            onChange={(e) => setContent(e.target.value)}
                            rows={8}
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        />
                        {errors.content && <p className="text-red-600 text-sm mt-1">{errors.content[0]}</p>}
                    </div>

                    <div className="flex items-center justify-end">
                        <button type="submit" className="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">
                            {isEditing ? 'Update' : 'Publish'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
