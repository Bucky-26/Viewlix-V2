@extends('developer.layouts.app')

@section('title', 'Create Blog Post')

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

    .form-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 2rem;
        max-width: 900px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        color: var(--dark);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-group input[type="text"],
    .form-group input[type="file"],
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .form-group input[type="text"]:focus,
    .form-group input[type="file"]:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .form-group textarea {
        resize: vertical;
        font-family: monospace;
    }

    .form-group .help-text {
        color: #6b7280;
        font-size: 0.9rem;
        margin-top: 0.35rem;
    }

    .error-message {
        color: var(--danger);
        font-size: 0.9rem;
        margin-top: 0.35rem;
    }

    .form-group input.is-invalid,
    .form-group textarea.is-invalid {
        border-color: var(--danger);
    }

    .checkbox-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkbox-wrapper input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--secondary);
    }

    .checkbox-wrapper label {
        margin-bottom: 0;
        cursor: pointer;
        font-weight: 600;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .button-group .btn {
        flex: 0;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 1.5rem;
        }

        .button-group {
            flex-direction: column;
        }

        .button-group .btn {
            width: 100%;
        }
    }
</style>

<section class="admin-header">
    <div class="container">
        <a href="{{ route('developer.admin.posts') }}" class="back-link">
            ← Back to Posts
        </a>
        <h1 style="font-size: 2rem; margin: 0;">Create Blog Post</h1>
    </div>
</section>

<section style="padding: 3rem 2rem;">
    <div class="container">
        <form action="{{ route('developer.admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="form-container">
            @csrf

            <!-- Title -->
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required 
                       @if($errors->has('title')) class="is-invalid" @endif
                       placeholder="Enter post title">
                @error('title')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Excerpt -->
            <div class="form-group">
                <label for="excerpt">Excerpt *</label>
                <textarea name="excerpt" id="excerpt" rows="4" required 
                          @if($errors->has('excerpt')) class="is-invalid" @endif
                          placeholder="Brief summary of your post">{{ old('excerpt') }}</textarea>
                <div class="help-text">A short summary of your post (shown in listings)</div>
                @error('excerpt')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Featured Image -->
            <div class="form-group">
                <label for="featured_image">Featured Image</label>
                <input type="file" name="featured_image" id="featured_image" accept="image/*" 
                       @if($errors->has('featured_image')) class="is-invalid" @endif>
                <div class="help-text">JPEG, PNG, JPG or GIF (max 2MB)</div>
                @error('featured_image')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Content -->
            <div class="form-group">
                <label for="content">Content *</label>
                <textarea name="content" id="content" rows="15" required 
                          @if($errors->has('content')) class="is-invalid" @endif
                          placeholder="Write your post content here...">{{ old('content') }}</textarea>
                @error('content')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <!-- Publish Status -->
            <div class="form-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="is_published" id="is_published" value="1" 
                           @if(old('is_published')) checked @endif>
                    <label for="is_published">Publish immediately</label>
                </div>
                <div class="help-text">Leave unchecked to save as draft</div>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button type="submit" class="btn btn-primary">
                    Create Post
                </button>
                <a href="{{ route('developer.admin.posts') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</section>

@endsection
