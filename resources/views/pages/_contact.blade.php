{{-- CONTACT --}}
<section id="contact" class="py-5">
    <div class="container">
        <h2 class="section-title text-center">Parlons de vos&nbsp;besoins</h2>
        <div class="gradient-bar"></div>
        <div class="row g-4 mt-1">
            <div class="col-lg-7">
                <div class="p-4 bg-white border rounded-4 shadow-soft h-100">
                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <x-forms.input name="name" label="Nom / Société" type="text" :required="true" placeholder="Ex. Dupont SARL" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input name="email" label="Email" type="email" :required="true" placeholder="vous@entreprise.com" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input name="phone" label="Téléphone" type="tel" :required="true" placeholder="06 12 34 56 78" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.select name="type" 
                                               label="Vous êtes" 
                                               :required="true"
                                               placeholder="-- Choisissez --"
                                               :options="[
                                                   'Individual' => 'Particulier',
                                                   'Association' => 'Association',
                                                   'Company' => 'Entreprise',
                                                   'Collectivity' => 'Collectivité'
                                               ]" />
                            </div>
                            <div class="col-12">
                                <x-forms.input name="need" 
                                               label="Votre besoin" 
											   placeholder="Décrivez votre demande..."
                                               type="textarea" 
                                               :required="true" 
                                               :rows="4" />
                            </div>

                            <div class="col-12 d-grid d-md-flex gap-2">
                                <button type="submit" class="btn btn-orange">
                                    <i class="fa-regular fa-paper-plane me-2"></i>
                                    Envoyer<span class="d-none d-sm-inline"> ma demande</span>
                                </button>
                                <a href="tel:+33149662177" class="btn btn-outline-orange">
                                    <i class="fa-regular fa-phone me-2"></i>
                                    01 49 66 21 77
                                </a>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">
                                    <i class="fa-regular fa-lock me-1"></i>Vos données ne sont ni revendues ni partagées.
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="p-4 bg-white  border rounded-4 shadow-soft h-100">
                    <h5 class="fw-bold mb-3">
                        <i class="fa-regular fa-clock me-2 text-orange"></i>Horaires & zones
                    </h5>
                    <ul class="list-unstyled text-secondary">
                        <li class="mb-2">
                            <i class="fa-regular fa-calendar-check me-2 text-orange"></i>Lun–Ven 9h–18h (astreinte en option)
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-map-location-dot me-2 text-orange"></i>À distance • Sur site (selon zone et/ou sur Devis)
                        </li>
                        <li class="mb-2">
                            <i class="fa-regular fa-file-signature me-2 text-orange"></i>Devis sous 24h ouvrées
                        </li>
                    </ul>
                    <hr class="my-4">
                    <h6 class="fw-bold">Besoin urgent ?</h6>
                    <p class="mb-2">Activez un ticket prioritaire :</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-orange w-100 w-sm-auto">
                        <i class="fa-regular fa-ticket me-1"></i>
                        Ouvrir un ticket
                    </a>
                    <hr class="my-4">
                    <img src="{{ asset('assets/img/logo/tv-logo.svg') }}" alt="TeamViewer" class="img-fluid logo-tv">
                    <p class="mt-4">
                        <a href="https://get.teamviewer.com/78369w6g" target="_blank" class="btn btn-primary w-100 w-sm-auto">
                            <i class="fa-regular fa-arrow-down-to-line"></i>
                            Obtenir<span class="d-none d-sm-inline"> le logiciel</span> TeamViewer
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>