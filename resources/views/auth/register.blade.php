@extends('layouts.auth')

@section('title', __('register.Register'))

@section('content')
    @if (session('verify_email_error'))
        <div class="alert alert-danger" role="alert">
            {{ session('verify_email_error') }}
        </div>
    @endif
    <div class="card rounded-4 shadow m-3">
        <div class="card-body">
            <img src="{{ asset('assets/img/logo/logo-horizontal.svg') }}" alt="Mon Support by Yellow Cactus"
                class="d-inline-block align-text-top mb-2 mx-3">
            <h5 class="card-title py-3">
                <i class="fa-regular fa-address-card"></i>
                {{ __('register.Create an account') }}
            </h5>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-floating form-field-start">
                    <x-forms.input name="email" type="email" :label="__('register.Email')" :value="old('email')" :required="true"
                        :labelAfter="true" autofocus />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="company" type="text" :label="__('register.CompanyName')" :value="old('company')" :required="true"
                        :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="firstname" type="text" :label="__('register.FirstName')" :value="old('firstname')" :required="true"
                        :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="lastname" type="text" :label="__('register.LastName')" :value="old('lastname')" :required="true"
                        :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="address_line1" type="text" :label="__('register.AddressLine1')" :value="old('address_line1')"
                        :required="true" :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="address_line2" type="text" :label="__('register.AddressLine2')" :value="old('address_line2')"
                        :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="zip" type="text" :label="__('register.Zip')" :value="old('zip')" :required="true"
                        :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="city" type="text" :label="__('register.City')" :value="old('city')" :required="true"
                        :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="phone" type="text" :label="__('register.Phone')" :value="old('phone')" :required="true"
                        :labelAfter="true" />
                </div>
                <div class="form-floating form-field-middle">
                    <x-forms.input name="password" type="password" :label="__('register.Password')" :required="true" :labelAfter="true"
                        id="password" />
                </div>
                <div class="form-floating mb-3 form-field-end">
                    <x-forms.input name="password_confirmation" type="password" :label="__('register.Confirm Password')" :required="true"
                        :labelAfter="true" />
                </div>
                <div class="mb-3 px-3">
                    <div class="progress mb-2" style="height: 5px;">
                        <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="password-requirements" class="small text-muted">
                        <div id="req-length" class="requirement">
                            <i id="icon-length" class="fa-regular fa-circle text-muted me-2"></i>Au moins 8 caractères
                        </div>
                        <div id="req-lowercase" class="requirement">
                            <i id="icon-lowercase" class="fa-regular fa-circle text-muted me-2"></i>Une minuscule
                        </div>
                        <div id="req-uppercase" class="requirement">
                            <i id="icon-uppercase" class="fa-regular fa-circle text-muted me-2"></i>Une majuscule
                        </div>
                        <div id="req-number" class="requirement">
                            <i id="icon-number" class="fa-regular fa-circle text-muted me-2"></i>Un chiffre
                        </div>
                        <div id="req-special" class="requirement">
                            <i id="icon-special" class="fa-regular fa-circle text-muted me-2"></i>Un caractère spécial
                            (!@#$%^&*)
                        </div>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input @error('agree_terms') is-invalid @enderror" 
                           id="agree_terms" 
                           name="agree_terms" 
                           value="1"
                           @if(old('agree_terms')) checked @endif
                           required>
                    <label class="form-check-label" for="agree_terms">
                        {!! __('register.AgreeTerms', ['cgu_link' => '<a href="' . route('cgu') . '" class="text-orange text-decoration-none" target="_blank">' . __('register.Terms of Use') . '</a>']) !!}
                        <span class="text-danger">*</span>
                    </label>
                    
                    @error('agree_terms')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-orange w-100 my-3">
                    {{ __('register.Register') }}
                </button>
            </form>
            <p>
                <a href="{{ route('home') }}" class="btn btn-link p-0 float-end text-secondary">
                    {{ __('global.Cancel') }}
                </a>
            </p>
        </div>
    </div>
    @include('emails._baseline2')

    <script>
        $(document).ready(function() {
            const $passwordInput = $('input[name="password"]');
            const $strengthBar = $('#password-strength-bar');

            function updateRequirement(iconId, elementId, met) {
                const $icon = $('#' + iconId);
                const $element = $('#' + elementId);

                if (met) {
                    // Condition OK : check vert
                    $icon.removeClass('fa-regular fa-circle text-muted')
                        .addClass('fa-solid fa-circle-check text-success me-2');
                    $element.removeClass('text-muted').addClass('text-success');
                } else {
                    // Condition pas OK : circle gris
                    $icon.removeClass('fa-solid fa-circle-check text-success')
                        .addClass('fa-regular fa-circle text-muted me-2');
                    $element.removeClass('text-success').addClass('text-muted');
                }
            }

            function calculateStrength(password) {
                const checks = {
                    length: password.length >= 8,
                    lowercase: /[a-z]/.test(password),
                    uppercase: /[A-Z]/.test(password),
                    number: /\d/.test(password),
                    special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
                };

                let strength = 0;
                Object.values(checks).forEach(check => {
                    if (check) strength += 20;
                });

                return {
                    strength,
                    checks
                };
            }

            function updateStrengthBar(strength) {
                $strengthBar.css('width', strength + '%');

                if (strength === 0) {
                    $strengthBar.attr('class', 'progress-bar');
                } else if (strength < 40) {
                    $strengthBar.attr('class', 'progress-bar bg-danger');
                } else if (strength < 80) {
                    $strengthBar.attr('class', 'progress-bar bg-warning');
                } else {
                    $strengthBar.attr('class', 'progress-bar bg-success');
                }
            }

            // Event listener pour le champ password
            $passwordInput.on('input', function() {
                const password = $(this).val();
                const {
                    strength,
                    checks
                } = calculateStrength(password);

                // Mettre à jour la barre de progression
                updateStrengthBar(strength);

                // Mettre à jour chaque requirement
                updateRequirement('icon-length', 'req-length', checks.length);
                updateRequirement('icon-lowercase', 'req-lowercase', checks.lowercase);
                updateRequirement('icon-uppercase', 'req-uppercase', checks.uppercase);
                updateRequirement('icon-number', 'req-number', checks.number);
                updateRequirement('icon-special', 'req-special', checks.special);
            });

            // Validation lors de la soumission
            $('form').on('submit', function(e) {
                const password = $passwordInput.val();
                const {
                    checks
                } = calculateStrength(password);

                const allRequirementsMet = Object.values(checks).every(check => check);

                if (!allRequirementsMet) {
                    e.preventDefault();
                    alert('Le mot de passe ne respecte pas tous les critères de sécurité requis.');
                    $passwordInput.focus();
                }
            });
        });
    </script>
@endsection
