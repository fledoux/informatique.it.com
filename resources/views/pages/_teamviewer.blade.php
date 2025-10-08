{{-- Team							<p class="text-secondary mb-0 small">Nous utilisons
								<a href="{{ config('app.teamviewer_url') }}" target="_blank" class="text-orange fw-bold">{{ config('app.teamviewer_name') }}</a>
								pour des&nbsp;sessions d'assistance à distance sécurisées, rapides et intuitives.</p>wer --}}
	<section class="py-5">
		<div class="container">
			<div class="hero border border-orange border-1 rounded-4 p-4">
				<div class="row mt-0">
					<div class="col-md-8 mx-auto">
						<div class="d-flex flex-column flex-sm-row align-items-center gap-1 gap-sm-3 mb-2">
							<div>
								<h6 class="fw-bold mb-1 text-orange">Connexion à distance avec {{ config('app.teamviewer_name') }}</h6>
								<p class="text-secondary mb-0 small">Nous utilisons
									<a href="{{ config('app.teamviewer_url') }}" target="_blank" class="text-orange fw-bold">{{ config('app.teamviewer_name') }}</a>
									pour des&nbsp;sessions d’assistance à distance sécurisées, rapides et intuitives.</p>
							</div>
							<img src="{{ asset('assets/img/logo/tv-logo.svg') }}" alt="{{ config('app.teamviewer_name') }}" style="height:30px;">
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>