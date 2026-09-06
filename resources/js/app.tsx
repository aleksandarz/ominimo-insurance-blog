import axios from 'axios';
import { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Routes, Route, Link } from 'react-router-dom';
import PostList from './components/PostList';
import PostDetail from './components/PostDetail';
import PostForm from './components/PostForm';
import { User } from './types';

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

function App() {
    const [currentUser, setCurrentUser] = useState<User | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axios.get('/sanctum/csrf-cookie').then(() => {
            axios.get('/api/user')
                .then((res) => setCurrentUser(res.data))
                .catch(() => setCurrentUser(null))
                .finally(() => setLoading(false));
        });
    }, []);

    if (loading) return <p className="p-6 text-gray-500">Loading...</p>;

    return (
        <BrowserRouter basename="/blog">
            <div className="max-w-4xl mx-auto p-6">
                <div className="flex justify-between items-center mb-6">
                    <Link to="/" className="text-xl font-semibold">Blog</Link>
                    {currentUser && (
                        <Link to="/posts/create" className="text-sm px-4 py-2 bg-gray-800 text-white rounded-md">
                            New Post
                        </Link>
                    )}
                </div>

                <Routes>
                    <Route path="/" element={<PostList />} />
                    <Route path="/posts/create" element={<PostForm />} />
                    <Route path="/posts/:id" element={<PostDetail currentUser={currentUser} />} />
                    <Route path="/posts/:id/edit" element={<PostForm />} />
                </Routes>
            </div>
        </BrowserRouter>
    );
}

const container = document.getElementById('app') as HTMLElement & { _reactRoot?: ReturnType<typeof createRoot> };

if (container) {
    if (!container._reactRoot) {
        container._reactRoot = createRoot(container);
    }
    container._reactRoot.render(<App />);
}