@extends('developer.layouts.app')

@section('title', $blogPost->title)

@section('content')
<style>
    .article-hero {
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        color: white;
        padding: 4rem 2rem;
    }

    .article-header-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
    }

    .article-container {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 3rem;
        margin-top: 3rem;
    }

    .article-content {
        background: white;
        padding: 3rem;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        line-height: 1.8;
        color: var(--dark);
    }

    .article-content h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 2rem 0 1rem 0;
        color: var(--dark);
    }

    .article-content h3 {
        font-size: 1.35rem;
        font-weight: 600;
        margin: 1.5rem 0 0.75rem 0;
        color: var(--dark);
    }

    .article-content p {
        margin-bottom: 1.5rem;
        color: #6b7280;
    }

    .article-content code {
        background: #f3f4f6;
        padding: 0.2rem 0.5rem;
        border-radius: 0.35rem;
        color: #d946ef;
        font-family: monospace;
    }

    .article-content pre {
        background: #1f2937;
        color: #e5e7eb;
        padding: 1.5rem;
        border-radius: 0.75rem;
        overflow-x: auto;
        margin: 1.5rem 0;
    }

    .sidebar {
        height: fit-content;
        position: sticky;
        top: 80px;
    }

    .sidebar-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .author-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .related-link {
        color: var(--secondary);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: block;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .related-link:last-child {
        border-bottom: none;
    }

    .related-link:hover {
        color: #4f46e5;
        padding-left: 0.5rem;
    }

    .back-link {
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-block;
        margin-bottom: 2rem;
    }

    .back-link:hover {
        opacity: 0.8;
    }

    @media (max-width: 768px) {
        .article-container {
            grid-template-columns: 1fr;
        }

        .sidebar {
            position: static;
        }

        .article-content {
            padding: 1.5rem;
        }

        .article-content h2 {
            font-size: 1.5rem;
        }
    }
</style>

<section class="article-hero">
    <div class="container">
        <a href="{{ route('developer.blogs') }}" class="back-link">
            ← Back to Blog
        </a>
        <h1 style="font-size: 2.5rem; margin-bottom: 1rem;">{{ $blogPost->title }}</h1>
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; opacity: 0.9;">
            <span>{{ $blogPost->published_at?->format('M d, Y') }}</span>
            <span>•</span>
            <span>By {{ $blogPost->author->name ?? 'Author' }}</span>
        </div>
        <p style="font-size: 1.1rem;">{{ $blogPost->excerpt }}</p>
    </div>
</section>

@if($blogPost->featured_image)
    <div style="padding: 2rem;">
        <div class="container">
            <img src="{{ $blogPost->featured_image }}" alt="{{ $blogPost->title }}" class="article-header-image">
        </div>
    </div>
@endif

<section style="padding: 3rem 2rem;">
    <div class="container">
        <div class="article-container">
            <!-- Main Content -->
            <article class="article-content">
                {!! nl2br(e($blogPost->content)) !!}
            </article>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- About Author -->
                <div class="sidebar-card">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; color: var(--dark);">About Author</h3>
                    <div style="text-align: center;">
                        @if($blogPost->author->avatar)
                            <img src="{{ $blogPost->author->avatar }}" alt="{{ $blogPost->author->name }}" class="author-avatar">
                        @else
                            <div class="author-avatar">👤</div>
                        @endif
                        <h4 style="font-weight: 600; color: var(--dark); margin-bottom: 0.25rem;">{{ $blogPost->author->name }}</h4>
                        <p style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1rem;">Full Stack Developer</p>
                        <p style="color: #6b7280; font-size: 0.95rem; line-height: 1.6;">
                            Sharing insights and knowledge about web development and technology.
                        </p>
                    </div>
                </div>

                <!-- Related Posts -->
                <div class="sidebar-card">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--dark);">More Posts</h3>
                    @php
                        $relatedPosts = \App\Models\BlogPost::published()
                            ->where('id', '!=', $blogPost->id)
                            ->latest('published_at')
                            ->take(3)
                            ->get();
                    @endphp

                    @forelse($relatedPosts as $post)
                        <a href="{{ route('developer.blog.show', $post->slug) }}" class="related-link">
                            <span style="display: block; font-weight: 600;">{{ $post->title }}</span>
                            <span style="font-size: 0.85rem; color: #9ca3af;">{{ $post->published_at->format('M d, Y') }}</span>
                        </a>
                    @empty
                        <p style="color: #6b7280; font-size: 0.95rem;">No other posts yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
