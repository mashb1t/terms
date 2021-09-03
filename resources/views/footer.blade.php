<footer tabindex="0" aria-label="footer" class="focus:outline-none mx-auto container flex flex-col items-center justify-center bg-white shadow">
    <div class="text-black flex flex-col md:items-center f-f-l">
        <div class="my-6 text-base text-color f-f-l">
            <ul class="md:flex items-center">
                <li class="py-2 md:py-0 lg:py-0 text-center">
                    <x-link href="{{ route('terms.show') }}">
                        {{ __('Terms') }}
                    </x-link>
                </li>
                <li class="py-2 md:py-0 lg:py-0 md:ml-6 text-center">
                    <x-link href="{{ route('policy.show') }}">
                        {{ __('Privacy Policy') }}
                    </x-link>
                </li>
            </ul>
        </div>
    </div>
</footer>

