@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-3xl font-bold mb-6">Our Books</h1>

    <!-- Search and filter options -->
    <div class="mb-8">
        <form id="search-form" class="flex items-center">
            <input type="text" name="search" placeholder="Search books..." class="form-input mr-2">
            <select name="category" class="form-select mr-2">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="sort" class="form-select mr-2">
                <option value="created_at">Newest</option>
                <option value="price">Price: Low to High</option>
                <option value="price-desc">Price: High to Low</option>
                <option value="title">Title</option>
            </select>
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    </div>

    <!-- Book grid -->
    <div id="book-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @include('partials.book-grid', ['books' => $books])
    </div>

    <!-- Pagination -->
    <div id="pagination" class="mt-8">
        {{ $books->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#search-form').on('submit', function(e) {
            e.preventDefault();
            fetchBooks();
        });

        function fetchBooks(page = 1) {
            $.ajax({
                url: '{{ route("home") }}' + '?page=' + page,
                data: $('#search-form').serialize(),
                success: function(response) {
                    $('#book-grid').html(response.books);
                    $('#pagination').html(response.pagination);
                }
            });
        }

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            fetchBooks(page);
        });
    });
</script>
@endpush