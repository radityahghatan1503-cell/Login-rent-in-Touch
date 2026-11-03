@if (! empty($m['foto']))
    <img src="{{ asset('storage/' . $m['foto']) }}" alt="{{ $m['nama'] }}" class="car-photo">
@else
<svg viewBox="0 0 300 110" role="img" aria-label="Ilustrasi mobil">
    <path d="M12 74c0-10 6-16 16-17l26-4 22-24c3-3 7-5 12-5h62c6 0 11 2 15 6l26 22 38 6c10 2 17 8 17 16v14c0 3-2 5-5 5H17c-3 0-5-2-5-5z" fill="#164a3d"/>
    <path d="M96 31h62c3 0 5 1 7 3l18 17H80z" fill="#6f9487"/>
    <circle cx="82" cy="88" r="20" fill="#164a3d"/><circle cx="82" cy="88" r="9" fill="#e8a23b"/>
    <circle cx="222" cy="88" r="20" fill="#164a3d"/><circle cx="222" cy="88" r="9" fill="#e8a23b"/>
</svg>
@endif
