@extends('developer.layouts.app')

@section('title', 'Blog Posts')

@section('content')
<style>
    .admin-header {
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        color: white;
        padding: 3rem 2rem;
    }

    .back-link {
        color: white;
        text-decoration: none;
        font-weight: 600;
        margin-bottom: 2rem;
        display: inline-block;
        transition: all 0.3s;
    }

    .back-link:hover {
        opacity: 0.8;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.75rem;
        margin-bottom: 2rem;
    }

    .alert-success {
        background: #dcfce7;
        border: 1px solid #86efac;
        color: #166534;
    }

    .posts-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .posts-table thead {
        background: #f9fafb;
    }

    .posts-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        border-bottom: 1px solid #e5e7eb;
    }

    .posts-table td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
    }

    .posts-table tr:last-child td {
        border-bottom: none;
    }

    .posts-table tbody tr:hover {
        background: #f9fafb;
    }

    .status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-published {
        background: #dcfce7;
        color: #166534;
    }

    .status-draft {
        background: #fef3c7;
        color: #92400e;
    }

    .action-icon {
        width: 18px;
        height: 18px;
        display: inline-block;
        margin-right: 0.5rem;
    }

    .action-link {
        color: var(--secondary);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        margin-right: 1rem;
    }

    .action-link:hover {
        color: #4f46e5;
    }

    .action-delete {
        color: var(--danger);
        text-decoration: none;
        font-weight: 600;
    }

    .action-delete:hover {
        color: #dc2626;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .table-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
    }

    @media (max-width: 768px) {
        .posts-table {
            font-size: 0.9rem;
        }

        .posts-table th,
        .posts-table td {
            padding: 0.75rem;
        }

        .table-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
    }
</style>

<section class="admin-header">
    <div class="container">
        <a href="{{ route('developer.admin.dashboard') }}" class="back-link">
            ← Back to Dashboard
        </a>
        <h1 style="font-size: 2rem; margin: 0;">Blog Posts</h1>
    </div>
</section>

<section style="padding: 3rem 2rem;">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-header">
            <h2>All Posts</h2>
            <a href="{{ route('developer.admin.posts.create') }}" class="btn btn-primary">
                + New Post
            </a>
        </div>

        @if($blogPosts->count() > 0)
            <table class="posts-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Published Date</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($blogPosts as $post)
                        <tr>
                            <td style="font-weight: 600; color: var(--dark);">{{ $post->title }}</td>
                            <td>
                                @if($post->is_published)
                                    <span class="status-badge status-published">Published</span>
                                @else
                                    <span class="status-badge status-draft">Draft</span>
                                @endif
                            </td>
                            <td>{{ $post->published_at?->format('M d, Y') ?? '-' }}</td>
                            <td>{{ $post->created_at->format('M d, Y') }}</td>
                            <td style="display: flex; gap: 1rem;">
                                <a href="{{ route('developer.blog.show', $post->slug) }}" class="action-link" title="View">
                                    👁️
                                </a>
                                <a href="{{ route('developer.admin.posts.edit', $post) }}" class="action-link" title="Edit">
                                    ✏️
                                </a>
                                <form action="{{ route('developer.admin.posts.destroy', $post) }}" method="POST" 
                                      style="display: inline;" onclick="return confirm('Delete this post?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-delete" style="border: none; background: none; cursor: pointer;" title="Delete">
                                        🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <p style="color: #6b7280; margin-bottom: 1rem;">No posts yet.</p>
                <a href="{{ route('developer.admin.posts.create') }}" class="btn btn-primary">
                    Create your first post
                </a>
            </div>
        @endif
    </div>
</section>

@endsection

    <!-- Pagination -->
    <div class="mt-6">
        {{ $blogPosts->links() }}
    </div>
</div>
@endsection
