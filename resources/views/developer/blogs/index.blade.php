@extends('developer.layouts.app')

@section('title', 'Blog Posts')

@section('content')
<style>
    .blog-header {
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        color: white;
        padding: 4rem 2rem;
    }

    .blog-list-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 2rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 2rem;
        align-items: start;
    }

    .blog-list-item:hover {
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
        border-color: var(--secondary);
    }

    .blog-thumbnail {
        width: 200px;
        height: 200px;
        border-radius: 0.75rem;
        object-fit: cover;
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        flex-shrink: 0;
    }

    .blog-info h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 0.75rem;
    }

    .blog-info h2:hover {
        color: var(--secondary);
    }

    .blog-meta {
        display: flex;
        gap: 1.5rem;
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .blog-excerpt {
        color: #6b7280;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .read-more {
        color: var(--secondary);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .read-more:hover {
        color: #4f46e5;
    }

    .back-link {
        color: white;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 2rem;
        display: inline-block;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        background: #f3f4f6;
        border-radius: 0.75rem;
    }

    @media (max-width: 768px) {
        .blog-list-item {
            grid-template-columns: 1fr;
        }

        .blog-thumbnail {
            width: 100%;
        }

        .blog-info h2 {
            font-size: 1.5rem;
        }
    }
</style>

<section class="blog-header">
    <div class="container">
        <a href="{{ route('developer.portfolio') }}" class="back-link">
            ← Back to Portfolio
        </a>
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Blog</h1>
        <p style="font-size: 1.1rem; opacity: 0.9;">Articles about development, technology, and web design</p>
    </div>
</section>

<section style="padding: 4rem 2rem;">
    <div class="container">
        @if($blogPosts->count() > 0)
            @foreach($blogPosts as $post)
                <a href="{{ route('developer.blog.show', $post->slug) }}" style="text-decoration: none; color: inherit;">
                    <div class="blog-list-item">
                        <div class="blog-thumbnail">
                            @if($post->featured_image)
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.75rem;">
                            @else
                                📝
                            @endif
                        </div>
                        <div class="blog-info">
                            <h2>{{ $post->title }}</h2>
                            <div class="blog-meta">
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                                <span>By {{ $post->author->name ?? 'Author' }}</span>
                            </div>
                            <p class="blog-excerpt">{{ $post->excerpt }}</p>
                            <span class="read-more">Read Article →</span>
                        </div>
                    </div>
                </a>
            @endforeach

            <!-- Pagination -->
            <div style="margin-top: 3rem; text-align: center;">
                {{ $blogPosts->links() }}
            </div>
        @else
            <div class="empty-state">
                <p style="color: #6b7280; font-size: 1.1rem;">No blog posts published yet. Check back soon!</p>
            </div>
        @endif
    </div>
</section>

@endsection
