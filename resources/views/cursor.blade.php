<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Cursor Listviewer</title>

    @vite('resources/css/app.css')
</head>
<body class="min-h-screen font-sans text-[0.9rem] leading-relaxed {{ $dark ? 'bg-main-dark text-muted-dark' : 'bg-gray-50 text-gray-900' }}">
    <div class="mx-auto max-w-5xl px-4">
        <h1 class="mt-4 mb-10 text-center text-5xl font-light">
            <a href="{{ $permalink ?? '' }}" class="{{ $dark ? 'text-muted-dark' : 'text-gray-900' }} hover:underline">Cursor Listviewer</a>
        </h1>

        @if ($unavailable)
            <div class="mb-6 rounded-md border p-4 text-center {{ $dark ? 'border-yellow-400/60 bg-card-dark' : 'border-yellow-400 bg-yellow-50' }}">
                The feed is currently unavailable. Please try again later.
            </div>
        @endif

        @foreach ($items as $entry)
            @php($item = $entry['item'])
            <article class="mb-4 rounded-lg p-6 shadow-md {{ $entry['new'] ? ($dark ? 'bg-new-dark' : 'bg-gray-100') : ($dark ? 'bg-card-dark' : 'bg-white') }}">
                <h3 class="text-2xl font-medium">
                    @if ($entry['new'])<span class="text-yellow-400">&bull;</span> @endif<a class="{{ $dark ? 'text-muted-dark' : 'text-gray-900' }} hover:underline" href="{{ $item->link }}">{{ $item->title }}</a>
                </h3>
                @if ($item->publishedAt)
                    <div class="mb-3 {{ $dark ? 'text-muted-dark/70' : 'text-gray-500' }}">{{ $item->publishedAt->format('l jS \of F Y, H:i:s') }}</div>
                @endif
                <p class="mb-3 text-justify">{{ $item->content }}</p>

                <a href="{{ $item->link }}" class="text-blue-500 hover:underline">Continue reading</a>
            </article>
        @endforeach

        <footer class="my-8 text-sm {{ $dark ? 'text-muted-dark' : 'text-gray-500' }}">
            The content is scraped from <a href="{{ config('feeds.url') }}" class="underline">cursor.tue.nl</a>'s rss feed. A cookie is used to create a php session, which is used to track last seen articles.
        </footer>
    </div>
</body>
</html>
