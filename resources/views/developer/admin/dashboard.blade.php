@extends('developer.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .admin-header {
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        color: white;
        padding: 3rem 2rem;
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin: 3rem 0;
    }

    .stat-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }

    .stat-icon.primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .stat-icon.success {
        background: #dcfce7;
        color: #166534;
    }

    .stat-icon.warning {
        background: #fef3c7;
        color: #92400e;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark);
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6b7280;
    }

    .recent-posts-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .recent-posts-table thead {
        background: #f9fafb;
    }

    .recent-posts-table th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--dark);
        border-bottom: 1px solid #e5e7eb;
    }

    .recent-posts-table td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #6b7280;
    }

    .recent-posts-table tr:last-child td {
        border-bottom: none;
    }

    .recent-posts-table tbody tr:hover {
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

    .action-link {
        color: var(--secondary);
        text-decoration: none;
        font-weight: 600;
        margin-right: 1rem;
        transition: all 0.3s;
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

    .empty-state {
        text-align: center;
        padding: 3rem;
        background: #f9fafb;
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }

        .recent-posts-table {
            font-size: 0.9rem;
        }

        .recent-posts-table th,
        .recent-posts-table td {
            padding: 0.75rem;
        }
    }
</style>

<section class="admin-header">
    <div class="container">
        <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Developer Admin Panel</h1>
        <p style="font-size: 1rem; opacity: 0.9;">Manage your blog posts and portfolio content</p>
    </div>
</section>

<section style="padding: 3rem 2rem;">
    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon primary">📊</div>
                <div>
                    <div class="stat-label">Total Posts</div>
                    <div class="stat-number">{{ $stats['total_posts'] }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon success">✓</div>
                <div>
                    <div class="stat-label">Published</div>
                    <div class="stat-number">{{ $stats['published_posts'] }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon warning">📝</div>
                <div>
                    <div class="stat-label">Drafts</div>
                    <div class="stat-number">{{ $stats['draft_posts'] }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Posts -->
        <div style="margin-top: 3rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--dark);">Recent Posts</h2>
                <a href="{{ route('developer.admin.posts.create') }}" class="btn btn-primary">
                    + Create Post
                </a>
            </div>

            @if($recentPosts->count() > 0)
                <table class="recent-posts-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Published</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPosts as $post)
                            <tr>
                                <td>
                                    <a href="{{ route('developer.blog.show', $post->slug) }}" class="action-link" style="margin-right: 0;">
                                        {{ $post->title }}
                                    </a>
                                </td>
                                <td>
                                    @if($post->is_published)
                                        <span class="status-badge status-published">Published</span>
                                    @else
                                        <span class="status-badge status-draft">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $post->published_at?->format('M d, Y') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('developer.admin.posts.edit', $post) }}" class="action-link">
                                        Edit
                                    </a>
                                    <form action="{{ route('developer.admin.posts.destroy', $post) }}" method="POST" 
                                          style="display: inline;" onclick="return confirm('Delete this post?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-delete" style="border: none; background: none; cursor: pointer;">
                                            Delete
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
                        Create Your First Post
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection
