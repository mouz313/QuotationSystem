@props(['label', 'name', 'checked' => false, 'value' => '1'])

<div style="display:flex;align-items:center;gap:.75rem;">
    <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;">
        <input type="checkbox" name="{{ $name }}" value="{{ $value }}" {{ $checked ? 'checked' : '' }} class="sr-only peer">
        <div style="width:2.75rem;height:1.5rem;background:var(--gray-200);border-radius:9999px;transition:background .2s;position:relative;cursor:pointer;" onmouseover="this.style.background='var(--gray-300)'" onmouseout="this.style.background=this.parentElement.querySelector('input').checked?'var(--brand-600)':'var(--gray-200)'">
            <div style="position:absolute;top:2px;left:2px;width:1.25rem;height:1.25rem;background:white;border-radius:9999px;transition:transform .2s;box-shadow:0 1px 3px rgba(0,0,0,.1);" class="peer-checked:translate-x-[1.25rem]"></div>
        </div>
        <script>
            (function(){
                var cb = document.currentScript.previousElementSibling.querySelector('input');
                var track = cb.nextElementSibling;
                var thumb = track.querySelector('div');
                function sync() {
                    track.style.background = cb.checked ? 'var(--brand-600)' : 'var(--gray-200)';
                    thumb.style.transform = cb.checked ? 'translateX(1.25rem)' : 'translateX(0)';
                }
                cb.addEventListener('change', sync);
                sync();
            })();
        </script>
    </label>
    <span style="font-size:.8125rem;color:var(--gray-700);">{{ $label }}</span>
</div>
