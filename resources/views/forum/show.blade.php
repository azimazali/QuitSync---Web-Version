<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Forum Discussion') }}
            </h2>
            <a href="{{ route('forum.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">
                &larr; Back to Forum
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <div class="border-b border-gray-100 pb-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $post->title }}</h1>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                                @if($post->risk_level === 'high') bg-red-100 text-red-800
                                @elseif($post->risk_level === 'moderate') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($post->risk_level) }} Risk
                            </span>

                            @if ($post->user_id === Auth::id())
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('forum.edit', $post) }}"
                                        class="text-gray-400 hover:text-blue-600 transition p-1 rounded hover:bg-blue-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('forum.destroy', $post) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this post?');"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-gray-400 hover:text-red-600 transition p-1 rounded hover:bg-red-50">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        Posted by <span class="font-medium text-gray-900">{{ $post->user->name }}</span>
                        on {{ $post->created_at->format('F j, Y \a\t g:i A') }}
                    </div>
                </div>

                <div class="prose max-w-none text-gray-800 leading-relaxed">
                    {!! nl2br(e($post->body)) !!}
                </div>



                <!-- Comments Section -->
                <div class="mt-8 border-t border-gray-200 pt-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Comments</h3>

                    <!-- Comment Form -->
                    <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-8">
                        @csrf
                        <div class="mb-4">
                            <label for="body" class="sr-only">Your Comment</label>
                            <textarea name="body" id="body" rows="3" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="What are your thoughts?"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Post Comment
                            </button>
                        </div>
                    </form>

                    <!-- Comments List -->
                    <div class="space-y-6">
                        @forelse ($post->comments as $comment)
                            <div class="flex space-x-4 p-4 bg-gray-50 rounded-xl relative group">
                                <div class="flex-shrink-0">
                                    <div
                                        class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($comment->user->name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm font-bold text-gray-900">{{ $comment->user->name }}</span>
                                            <span
                                                class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if ($comment->user_id === Auth::id())
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                                                onsubmit="return confirm('Delete this comment?');"
                                                class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-gray-400 hover:text-red-600 text-xs">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-700 leading-relaxed">
                                        {{ $comment->body }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center italic py-4">No comments yet. Be the first to share your
                                thoughts!</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>