@foreach ($audits as $audit)
    <tr>
        <td class="py-4 text-black font-weight-600">{{ user_date_time($audit->created_at, true) }}</td>
        <td class="py-4 text-neutral-text-gray font-weight-600 text-center">{{ $audit->user->contactInformation->name ?? '' }}
        </td>
        <td class="py-4 text-neutral-text-gray font-weight-600 text-center">{{ $audit->object_name }}</td>
        <td class="py-4 text-neutral-text-gray font-weight-600 text-center">{{ $audit->event }}</td>
        <td>
            {!! $audit->custom_message ?? '' !!}
        </td>
    </tr>
@endforeach
