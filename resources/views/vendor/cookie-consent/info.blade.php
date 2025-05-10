@foreach($cookies->getCategories() as $category)
<h3 class="text-white">{{ $category->title }}</h3>
<table>
    <thead>
        <th>@lang('messages.cookies.cookie')</th>
        <th>@lang('messages.cookies.purpose')</th>
        <th>@lang('messages.cookies.duration')</th>
    </thead>
    <tbody>
    @foreach($category->getCookies() as $cookie)
        <tr>
            <td>{{ $cookie->name }}</td>
            <td>{{ $cookie->description }}</td>
            <td>{{ \Carbon\CarbonInterval::minutes($cookie->duration)->cascade() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endforeach
