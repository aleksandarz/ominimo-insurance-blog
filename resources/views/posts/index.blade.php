<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Blog Posts') }}
            </h2>
            @auth
                <a href="{{ route('posts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('New Post') }}
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($posts as $post)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <a href="{{ route('posts.show', $post) }}" class="text-lg font-semibold text-gray-900 hover:underline">
                        {{ $post->title }}
                    </a>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('by') }} {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}
                    </p>
                    <p class="text-gray-700 mt-3">
                        {{ Str::limit($post->content, 150) }}
                    </p>
                </div>
            @empty
                <p class="text-gray-500">{{ __('No posts yet.') }}</p>
            @endforelse

            {{ $posts->links() }}
        </div>
    </div>
</x-app-layout>