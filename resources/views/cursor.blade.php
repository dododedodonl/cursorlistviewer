<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Cursor Listviewer</title>
  </head>
  <body>
    <div class="container">
        <h1 class="mt-3 mb-5 display-4 text-center"><a href="{{ $permalink ?? '' }}" class="text-dark">Cursor Listviewer</a></h1>

        @foreach ($items as  $item)
        <div class="row mb-2">
            <div class="col-md-12">
              <div class="card flex-md-row mb-4 box-shadow h-md-250{{$item['new'] ? ' bg-light' : '' }}">
                  {{-- <img class="card-img-left flex-auto d-none d-md-block" data-src="holder.js/200x250?theme=thumb" alt="Thumbnail [200x250]" src="" style="width: 200px; height: 250px;"> --}}

                <div class="card-body d-flex flex-column align-items-start">
                  {{-- <strong class="d-inline-block mb-2 text-success">Design</strong> --}}
                  <h3 class="mb-0">
                    {!! $item['new'] ? '<span class="text-warning">&bull;</span> ' : '' !!}<a class="text-dark" href="{{ $item['link'] }}">{{ $item['title'] }}</a>
                  </h3>
                  <div class="mb-3 text-muted">{{ $item['date'] }}</div>
                  <p class="card-text text-justify mb-3">{{ $item['content'] }}</p>

                  <a href="{{ $item['link'] }}">Continue reading</a>
                </div>
                 </div>
            </div>
        </div>
        @endforeach

        <footer>
            The content is scraped from <a href="https://cursor.tue.nl/rss.xml">cursor.tue.nl</a>'s rss feed.
        </footer>
    </div>

    {{-- <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script> --}}
  </body>
</html>
