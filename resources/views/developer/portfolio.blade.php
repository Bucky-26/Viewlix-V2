@extends('developer.layouts.app')

@section('title', 'Developer Portfolio')
@endsection

@section('content')
<style>
    .hero-section {
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        color: white;
        padding: 6rem 2rem;
        text-align: center;
    }

    .hero-section h1 {
        color: white;
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .hero-section .subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }

    .section-padding {
        padding: 4rem 2rem;
    }

    .stat-card {
        text-align: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #f3f4f6 0%, #ffffff 100%);
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--secondary);
    }

    .blog-card {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        transition: all 0.3s;
    }

    .blog-card:hover {
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
        transform: translateY(-4px);
    }

    .blog-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
    }

    .blog-content {
        padding: 1.5rem;
    }

    .blog-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--dark);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-excerpt {
        color: #6b7280;
        margin-bottom: 1rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .blog-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        color: #9ca3af;
        margin-bottom: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }

    .skill-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .skill-card {
        text-align: center;
        padding: 2rem;
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        transition: all 0.3s;
    }

    .skill-card:hover {
        border-color: var(--secondary);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.1);
    }

    .skill-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .skill-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--dark);
    }

    .about-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
        align-items: center;
    }

    .about-text p {
        font-size: 1.1rem;
        color: #6b7280;
        margin-bottom: 1.5rem;
        line-height: 1.8;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .hero-section h1 {
            font-size: 2rem;
        }

        .about-content {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .skill-grid {
            grid-template-columns: 1fr;
        }

        .grid-3 {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Hero Section -->
<section class="hero-section">
    <h1>Hi, I'm Your Name</h1>
    <p class="subtitle">Full-Stack Developer & Creative Problem Solver</p>
</section>

<!-- Stats Section -->
<section class="section-padding">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">5+</div>
                <p>Years Experience</p>
            </div>
            <div class="stat-card">
                <div class="stat-number">50+</div>
                <p>Projects Completed</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="section-padding" style="background: #f3f4f6;">
    <div class="container">
        <h2 style="font-size: 2rem; margin-bottom: 2rem; color: var(--dark);">About Me</h2>
        <div class="about-content">
            <div class="about-text">
                <p>
                    I'm a passionate full-stack developer with a love for building beautiful, functional web applications. 
                    With 5+ years of experience, I've had the pleasure of working on diverse projects ranging from startups to enterprise solutions.
                </p>
                <p>
                    My expertise spans modern web technologies including Laravel, Vue.js, React, and cloud platforms. 
                    I believe in writing clean, maintainable code and creating delightful user experiences.
                </p>
                <p>
                    When I'm not coding, you'll find me writing technical articles, contributing to open-source, or exploring new technologies.
                </p>
            </div>
            <div>
                <h3 style="font-weight: 600; margin-bottom: 1rem; color: var(--dark);">Technologies</h3>
                <div class="skill-grid">
                    <div class="skill-card">
                        <div class="skill-icon">🚀</div>
                        <div class="skill-title">Backend</div>
                        <p style="color: #6b7280; font-size: 0.9rem;">PHP, Laravel, Node.js</p>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">🎨</div>
                        <div class="skill-title">Frontend</div>
                        <p style="color: #6b7280; font-size: 0.9rem;">Vue.js, React, Tailwind</p>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">💾</div>
                        <div class="skill-title">Database</div>
                        <p style="color: #6b7280; font-size: 0.9rem;">MySQL, PostgreSQL, Firebase</p>
                    </div>
                    <div class="skill-card">
                        <div class="skill-icon">☁️</div>
                        <div class="skill-title">DevOps</div>
                        <p style="color: #6b7280; font-size: 0.9rem;">AWS, Docker, CI/CD</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recent Blog Posts -->
<section class="section-padding">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2 style="font-size: 2rem; color: var(--dark);">Latest Articles</h2>
            <a href="{{ route('developer.blogs') }}" style="color: var(--secondary); text-decoration: none; font-weight: 600;">
                Read All Articles →
            </a>
        </div>

        @if($blogPosts->count() > 0)
            <div class="grid-3">
                @foreach($blogPosts as $post)
                    <div class="blog-card">
                        <div class="blog-image">
                            @if($post->featured_image)
                                <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                📝
                            @endif
                        </div>
                        <div class="blog-content">
                            <div class="blog-title">{{ $post->title }}</div>
                            <div class="blog-excerpt">{{ $post->excerpt }}</div>
                            <div class="blog-meta">
                                <span>{{ $post->published_at->format('M d, Y') }}</span>
                                <span>{{ Auth::check() && Auth::user()->id === $post->author_id ? 'You' : $post->author->name }}</span>
                            </div>
                            <a href="{{ route('developer.blog.show', $post->slug) }}" class="btn btn-primary btn-small" style="width: 100%; display: block; text-align: center;">
                                Read Article
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 3rem; background: #f3f4f6; border-radius: 0.75rem;">
                <p style="color: #6b7280; font-size: 1.1rem;">No blog posts yet. Check back soon!</p>
            </div>
        @endif
    </div>
</section>

<!-- CTA Section -->
<section style="background: linear-gradient(135deg, var(--secondary) 0%, #8b5cf6 100%); color: white; padding: 4rem 2rem; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Let's Work Together</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.9;">I'd love to hear from you. Feel free to reach out!</p>
        <a href="mailto:hello@example.com" class="btn" style="background: white; color: var(--dark); padding: 0.75rem 2rem; display: inline-block;">
            Send Me an Email
        </a>
    </div>
</section>

@endsection
