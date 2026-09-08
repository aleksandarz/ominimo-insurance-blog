import axios from 'axios';
import { useEffect, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Routes, Route, Link, Navigate, useLocation } from 'react-router-dom';
import PostList from './components/PostList';
import PostDetail from './components/PostDetail';
import PostForm from './components/PostForm';
import { User } from './types';

axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;

function PageHeader({ currentUser }: { currentUser: User | null }) {
    const { pathname } = useLocation();

    let title = 'Blog Posts';
    if (pathname === '/posts/create') {
        title = 'Create New Post';
    } else if (pathname.endsWith('/edit')) {
        title = 'Edit Post';
    }

    return (
        <header className="bg-white shadow">
            <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">{title}</h2>
                {currentUser && pathname === '/' && (
                    <Link
                        to="/posts/create"
                        className="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        New Post
                    </Link>
                )}
            </div>
        </header>
    );
}

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
            <PageHeader currentUser={currentUser} />
            <main className="py-12">
                <Routes>
                    <Route path="/" element={<PostList />} />
                    <Route
                        path="/posts/create"
                        element={currentUser ? <PostForm /> : <Navigate to="/" replace />}
                    />
                    <Route path="/posts/:id" element={<PostDetail currentUser={currentUser} />} />
                    <Route
                        path="/posts/:id/edit"
                        element={currentUser ? <PostForm /> : <Navigate to="/" replace />}
                    />
                </Routes>
            </main>
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
