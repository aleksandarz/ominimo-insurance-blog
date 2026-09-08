<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <p class="text-sm text-gray-500">
                    {{ __('by') }} {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}
                </p>
                <div class="mt-4 text-gray-800 whitespace-pre-line">{{ $post->content }}</div>

                @can('update', $post)
                    <div class="mt-6 flex items-center space-x-3">
                        <a href="{{ route('posts.edit', $post) }}" class="text-sm text-indigo-600 hover:underline">{{ __('Edit') }}</a>

                        <form method="POST" action="{{ route('posts.destroy', $post) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')" class="m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Delete') }}</button>
                        </form>
                    </div>
                @endcan
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="font-semibold text-lg text-gray-800 mb-4">
                    {{ __('Comments') }} ({{ $post->comments->count() }})
                </h3>

                <div class="space-y-4">
                    @forelse ($post->comments as $comment)
                        <div class="border-b pb-3">
                            <div class="flex justify-between items-start">
                                <p class="text-sm font-semibold text-gray-700">
                                    {{ $comment->user->name ?? $comment->guest_name . ' (' . __('guest') . ')' }}
                                </p>
                                @can('delete', $comment)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:underline">{{ __('Delete') }}</button>
                                    </form>
                                @endcan
                            </div>
                            <p class="text-gray-600 mt-1">{{ $comment->comment }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">{{ __('No comments yet.') }}</p>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('comments.store', $post) }}" class="mt-6 space-y-4">
                    @csrf

                    @guest
                        <div>
                            <x-input-label for="guest_name" :value="__('Your Name')" />
                            <x-text-input id="guest_name" name="guest_name" type="text" class="mt-1 block w-full" :value="old('guest_name')" required />
                            <x-input-error :messages="$errors->get('guest_name')" class="mt-2" />
                        </div>
                    @endguest

                    <div>
                        <x-input-label for="comment" :value="__('Comment')" />
                        <textarea id="comment" name="comment" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('comment') }}</textarea>
                        <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                    </div>

                    <x-primary-button>{{ __('Add Comment') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>