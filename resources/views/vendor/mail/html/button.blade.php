@props([
    'url',
    'color' => 'primary', // Có thể là: primary, success, error hoặc mã hex
    'align' => 'center',
])

@php
    $colors = [
        'primary' => '#3490dc',
        'success' => '#38c172',
        'error'   => '#e3342f',
    ];

    $bgColor = $colors[$color] ?? $color;
@endphp

<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="{{ $align }}">
            <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td>
                        <a href="{{ $url }}"
                           target="_blank"
                           rel="noopener"
                           style="
                    display: inline-block;
                    background-color: {{ $bgColor }};
                    color: #ffffff;
                    font-size: 16px;
                    font-weight: bold;
                    text-decoration: none;
                    padding: 12px 24px;
                    border-radius: 6px;
                    text-align: center;
                ">
                            {{ $slot }}
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
