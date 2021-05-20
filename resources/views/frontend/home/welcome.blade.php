@extends('frontend.layouts.app')

@section('content')
<div class="site-blocks-cover" style="overflow: hidden;">
	<div class="container">
	  <div class="row align-items-center justify-content-center">
		<div class="col-md-12" style="position: relative;" data-aos="fade-up" data-aos-delay="200">
		  <img src="{{ asset('frontend/img/3014540.svg') }}" alt="Image" class="img-fluid img-absolute">
		  <div class="row mb-4" data-aos="fade-up" data-aos-delay="200">
			<div class="col-lg-6 mr-auto">
			  <h1>Die All-in-One-Lösung für Dienstleister</h1>
			  <p class="mb-5">Mit der Alfacockpit Software bieten Sie exzellente Kundenbetreuung und einfache Bearbeitungsprozesse an einem Ort. Behalten Sie immer den Überblick über den aktuellen Stand Ihrer Projekte und den erforderlichen Informationen und bearbeiten Sie Kundenanfragen schnell und einfach mit einem Klick. </p>
			  <div>
				<a href="#price-section" class="btn btn-primary mr-2 mb-2">Kostenlos herunterladen</a>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
  <div class="site-section" id="features-section">
	<div class="container">
	  <div class="row mb-5 justify-content-center text-center" data-aos="fade-up">
		<div class="col-7 text-center  mb-5">
		  <h2 class="section-title">Alles auf einen Blick</h2>
		  <p class="lead">Garantieren Sie effiziente Arbeitsprozesse und optimale Kundenbetreuung und steigern Sie Ihren Mehrwert.</p>
		</div>
	  </div>
	  <div class="row align-items-stretch">
		<div class="col-md-6 col-lg-4 mb-4 mb-lg-4" data-aos="fade-up">
		  <div class="unit-4 d-block">
			<div class="unit-4-icon mb-3">
			  <span class="icon-wrap"><span class="text-primary icon-sentiment_satisfied"></span></span>
			</div>
			<div>
			  <h3>Teamarbeit & Kollaboration</h3>
			  <p>Transparenter Überblick über alle Aktivitäten inklusive Benachrichtigungsfunktion. So sind alle Kollegen auf dem neuesten Stand.</p>
			</div>
		  </div>
		</div>
		<div class="col-md-6 col-lg-4 mb-4 mb-lg-4" data-aos="fade-up" data-aos-delay="100">
		  <div class="unit-4 d-block">
			<div class="unit-4-icon mb-3">
			  <span class="icon-wrap"><span class="text-primary icon-clear_all"></span></span>
			</div>
			<div>
			  <h3>Übersichtliche Kontakthistorie</h3>
			  <p>Alle Adress- und Kundendaten auf einen Blick. Inklusive E-Mails, Notizen, To-Dos und Termine.</p>
			</div>
		  </div>
		</div>
		<div class="col-md-6 col-lg-4 mb-4 mb-lg-4" data-aos="fade-up" data-aos-delay="200">
		  <div class="unit-4 d-block">
			<div class="unit-4-icon mb-3">
			  <span class="icon-wrap"><span class="text-primary icon-mobile"></span></span>
			</div>
			<div>
			  <h3>Jederzeit & von jedem Gerät Zugriff</h3>
			  <p>Browserbasiert und mit jedem internetfähigen Gerät nutzbar, auch mobil. Keine Installation nötig.</p>
			</div>
		  </div>
		</div>
		<div class="col-md-6 col-lg-4 mb-4 mb-lg-4" data-aos="fade-up">
		  <div class="unit-4 d-block">
			<div class="unit-4-icon mb-3">
			  <span class="icon-wrap"><span class="text-primary icon-autorenew"></span></span>
			</div>
			<div>
			  <h3>Einfach Anpassbar</h3>
			  <p>Antworten Sie effizient auf Kundenanfragen mit konfigurierbaren Mustertexten für E-Mail-Antworten. Ob E-Mail oder Online-Kontaktformular, alle Anfragen werden automatisch an einem Ort als Tickets erfasst.</p>
			</div>
		  </div>
		</div>
		<div class="col-md-6 col-lg-4 mb-4 mb-lg-4" data-aos="fade-up" data-aos-delay="100">
		  <div class="unit-4 d-block">
			<div class="unit-4-icon mb-3">
			  <span class="icon-wrap"><span class="text-primary icon-settings_backup_restore"></span></span>
			</div>
			<div>
			  <h3>Zuverlässiges Projektmanagement</h3>
			  <p>Koordinieren Sie einfach und schnell Projektaufgaben innerhalb Ihres Teams und behalten Sie den Überblick über den Status und Fristen einzelner Teilaufgaben. </p>
			</div>
		  </div>
		</div>
		<div class="col-md-6 col-lg-4 mb-4 mb-lg-4" data-aos="fade-up" data-aos-delay="200">
		  <div class="unit-4 d-block">
			<div class="unit-4-icon mb-3">
			  <span class="icon-wrap"><span class="text-primary icon-clock-o"></span></span>
			</div>
			<div>
			  <h3>Personalzeiterfassung</h3>
			  <p>Reduzieren Sie Ihren administrativen Aufwand und lassen Sie Ihre Mitarbeiter ganz einfach und transparent per Knopfdruck Ihre Arbeitszeit erfassen. </p>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
  <div class="site-section bg-light" id="price-section">
	<div class="container">
	  <div class="row mb-5">
		<div class="col-12 text-center">
		  <h2 class="section-title mb-3">Preise</h2>
		</div>
	  </div>
		<div class="pricing card-deck flex-column flex-md-row mb-3">
			<div class="card card-pricing text-center px-3 mb-4" data-aos="fade-right" data-aos-delay="200">
				<span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">Free</span>
				<div class="bg-transparent card-header pt-4 border-0">
					<h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="15"><span class="price">0</span>€</h1>
				</div>
				<div class="card-body pt-0">
					<ul class="list-unstyled mb-4">
						<li>self-hosted</li>
					</ul>
					

					
					{{-- <div class="btn-group">
						<a href="https://wawihost.com/alfacockpit" class="btn btn-primary mb-3" target="_blank">Download</a>
						<a class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						  <span class="sr-only">Toggle Dropdown</span>
						</a>
						<div class="dropdown-menu">
						  ...
						</div>
					  </div> --}}
					  @if($versions->count() > 0)
					  <div class="btn-group">
						<a class="text-white btn btn-outline-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						  Download
						</a>
						<div class="dropdown-menu">
						  <ul>
							  @foreach ($versions as $version)
							  	<li><a href="{{ route('version.download',$version->id) }}">{{ $version->version }}</a></li>
							  @endforeach
						  </ul>
						</div>
					  </div>
					  @else
					  	<a href="https://wawihost.com/alfacockpit" class="btn btn-outline-secondary mb-3" target="_blank">Download</a>
					  @endif
					   
				
				</div>
			</div>
			<div class="card card-pricing popular shadow text-center px-3 mb-4" data-aos="fade-left" data-aos-delay="200">
				<span class="h6 w-60 mx-auto px-4 py-1 rounded-bottom bg-primary text-white shadow-sm">Standard</span>
				<div class="bg-transparent card-header pt-4 border-0">
					<h1 class="h1 font-weight-normal text-primary text-center mb-0" data-pricing-value="30"><span class="price">14,99</span>€<span class="h6 text-muted ml-2">/ pro Monat</span></h1>
				</div>
				<div class="card-body pt-0">
					<ul class="list-unstyled mb-4">
						<li>Cloud Hosted</li>
					</ul>
					<a href="https://wawihost.com/alfacockpit" class="btn btn-primary mb-3" target="_blank">Order Now</a>
				</div>
			</div>
		</div>
	</div>
  </div>
  <div class="feature-big">
	<div class="container">
	  <div class="row mb-5 site-section">
		<div class="col-lg-7" data-aos="fade-right">
		  <img src="{{ asset('frontend/img/undraw_investing_7u74.svg') }}" alt="Image" class="img-fluid">
		</div>
		<div class="col-lg-5 pl-lg-5 ml-auto mt-md-5">
		  <h2 class="text-black">So sind Sie immer auf dem neusten Stand</h2>
		  <p class="mb-4">Das übersichtliche Dashboard hilft Ihnen dabei alle Vorgänge für Ihr Qualitätsmanagement auszuwerten und somit effektiv Ihre Produktivität zu steigern.</p>
		</div>
	  </div>
	  <div class="mt-5 row mb-5 site-section ">
		<div class="col-lg-7 order-2 order-lg-2" data-aos="fade-left">
		  <img src="{{ asset('frontend/img/undraw_metrics_gtu7.svg') }}" alt="Image" class="img-fluid">
		</div>
		<div class="col-lg-5 pr-lg-5 mr-auto mt-5 order-1 order-lg-1">
		  <h2 class="text-black">Optimieren Sie Ihre Arbeitsprozesse</h2>
		  <p class="mb-4">Die einfachen Analysefunktionen von Alfacockpit erleichtern Ihnen die Verbesserung Ihrer Vertriebsprozesse.</p>
		</div>
	  </div>
	</div>
  </div>
  <div class="site-section bg-light" id="about-section">
	<div class="container">
	  <div class="row mb-5">
		<div class="col-12 text-center">
		  <h2 class="section-title mb-3">Über uns</h2>
		</div>
	  </div>
	  <div class="row mb-5">
		<div class="col-lg-6" data-aos="fade-right">
		  <img src="{{ asset('frontend/img/undraw_bookmarks_r6up.svg') }}" alt="Image" class="img-fluid">
		</div>
		<div class="col-lg-5 ml-auto pl-lg-5">
		  <p class="mb-4">Alfacockpit ist ideal für Dienstleistungsunternehmen, die Ihren Fokus auf langfristige Kundenbeziehungen legen. Dabei spielt es keine Rolle ob Sie im Büro oder im Homeoffice arbeiten, denn Alfacockpit funktioniert online und ist Browserbasiert, überall wo Sie sind. Noch Fragen? Dann kontaktieren Sie uns jetzt, wir helfen Ihnen jederzeit gerne weiter.</p>
		</div>
	  </div>
	</div>
  </div>
  <div class="site-section bg-image2 overlay" id="contact-section"
	style="background-image: url('{{ asset('frontend/img/contact-bg.jpg') }}');">
	<div class="container">
	  <div class="row mb-5">
		<div class="col-12 text-center">
		  <h2 class="section-title mb-3 text-white">Kontakt</h2>
		</div>
	  </div>
	  <div class="row justify-content-center">
		<div class="col-lg-7 mb-5">
		@if(session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
		@endif
		  <form class="p-5 bg-white" method="POST" action="/contact">
			@csrf
			<h2 class="h4 text-black mb-5">Kontakt Formular</h2>
			<div class="row form-group {{ $errors->has('fname') ? 'has-error' : '' }}">
			  <div class="col-md-6 mb-3 mb-md-0">
				<label class="text-black" for="fname">Vorname</label>
				<input name="fname" type="text" id="fname" value="{{ old('fname') }}" class="form-control rounded-0">
				<span class="text-danger">{{ $errors->first('fname') }}</span>
			  </div>
			  <div class="col-md-6">
				<label class="text-black" for="lname">Nachname</label>
				<input name="lname" type="text" id="lname" value="{{ old('lname') }}" class="form-control rounded-0">
				<span class="text-danger">{{ $errors->first('lname') }}</span>
			  </div>
			</div>
			<div class="row form-group {{ $errors->has('email') ? 'has-error' : '' }}">
			  <div class="col-md-12">
				<label class="text-black" for="email">E-mail</label>
				<input name="email" type="email" id="email" value="{{ old('email') }}" class="form-control rounded-0">
				<span class="text-danger">{{ $errors->first('email') }}</span>
			  </div>
			</div>
			<div class="row form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
				<div class="col-md-12">
				  <label class="text-black" for="mobile">Telefonnummer</label>
				  <input name="mobile" type="text" id="mobile" value="{{ old('mobile') }}" class="form-control rounded-0">
				  <span class="text-danger">{{ $errors->first('mobile') }}</span>
				</div>
			</div>
			<div class="row form-group">
			  <div class="col-md-12">
				<label class="text-black" for="message">Nachricht</label>
				<textarea name="message" id="message" cols="30" rows="7" value="{{ old('message') }}" class="form-control rounded-0"
				  placeholder="Hinterlassen Sie Ihre Nachricht hier..."></textarea>
			  </div>
			</div>
			<div class="row form-group">
			  <div class="col-md-12">
				<input type="submit" value="Senden" class="btn btn-primary mr-2 mb-2">
			  </div>
			</div>
		  </form>
		</div>
	  </div>
	</div>
  </div>
@endsection
