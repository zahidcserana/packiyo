<tr>
<td class="header">
<table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
@if ($headerImage)
<img src="{{ $headerImage }}" class="header-image" alt="{{ __('Header image') }}">
@else
{{ $slot }}
@endif
</td>
</tr>
</table>
</td>
</tr>
