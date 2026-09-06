import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import axios from 'axios';
import { ValidationErrors } from '../types';

export default function PostForm() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEditing = Boolean(id);

    const [title, setTitle] = useState('');
    const [content, setContent] = useState('');
    const [errors, setErrors] = useState<ValidationErrors>({});

    useEffect(() => {
        if (isEditing) {
            axios.get(`/api/posts/${id}`).then((res) => {
                setTitle(res.data.data.title);
                setContent(res.data.data.content);
            });
        }
    }, [id]);

    const submit = async (e: React.FormEvent) => {
        e.preventDefault();
        setErrors({});

        try {
            if (isEditing) {
                await axios.put(`/api/posts/${id}`, { title, content });
                navigate(`/posts/${id}`);
            } else {
                const res = await axios.post('/api/posts', { title, content });
                navigate(`/posts/${res.data.data.id}`);
            }
        } catch (err: any) {
            if (err.response?.status === 422) {
                setErrors(err.response.data.errors);
            }
        }
    };

    return (
        <div className="bg-white p-6 shadow-sm rounded-lg max-w-2xl">
            <h2 className="text-xl font-semibold mb-4">{isEditing ? 'Edit Post' : 'Create New Post'}</h2>

            <form onSubmit={submit} className="space-y-4">
                <div>
                    <label className="block text-sm font-medium">Title</label>
                    <input
                        type="text"
                        value={title}
                        onChange={(e) => setTitle(e.target.value)}
                        className="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
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
                    />
                    {errors.content && <p className="text-red-600 text-sm mt-1">{errors.content[0]}</p>}
                </div>

                <button type="submit" className="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">
                    {isEditing ? 'Update' : 'Publish'}
                </button>
            </form>
        </div>
    );
}