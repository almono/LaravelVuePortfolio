<!DOCTYPE html>
<html style="margin: 0; padding: 0">

<head>
<!--    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>-->
    <link href="{{ public_path('css/bootstrap.css') }}"  rel="stylesheet" type="text/css">
<body>
    <div class="col-sm-12">
        <div class="col-sm-12 main-section">
            <div class="col-12">
                <div class="col-md-9 text-left padding-fix main-info" style="border: 1px solid green; display: flex;">
                    <h3 class="font-weight-bold mb-2" style="width: 100%;">Curriculum Vitae</h3>
                    <h5 class="font-weight-bold mb-2" style="width: 100%;">Pawel Walczykiewicz</h5>
                    <div class="col-sm-12 padding-fix">
                        @foreach($cvData['main_info'] as $info)
                            <span class="info-label">{{ $info['info_title']  }}:</span>
                            @if($info['info_title'] == 'Github' || $info['info_title'] == 'LinkedIn')
                                <a href="{{ $info['info_desc'] }}" target="_blank">{{ $info['info_desc'] }}</a>
                            @else
                                <span>{{ $info['info_desc'] }}</span>
                            @endif
                            <br>
                        @endforeach
                    </div>
                </div>
                <div class="col-sm-2 text-left padding-fix main-picture" style="border: 1px solid red; display: flex;">
                    <!--<img style="max-width: 100%" src="/portfolio/public/storage/CV_Photo.jpg"/>-->
                    <span style="max-width: 100%">aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa</span>
                </div>
                <div class="col-sm-12">
                    YEAHHHHHHHHHHHHH
                </div>
            </div>
        </div>
        @if($educationEnabled)
            <div class="col-12 eduction-section">

            </div>
        @endif
        @if($experienceEnabled)
            <div class="col-12 experience-section">

            </div>
        @endif
        @if($skillsEnabled)
            <div class="col-12 skills-section">

            </div>
        @endif
        @if($interestsEnabled)
            <div class="col-12 interests-section">

            </div>
        @endif
        @if($otherEnabled)
            <div class="col-12 others-section">

            </div>
        @endif
    </div>
</body>

</html>

<style>
    .main-card > .card-body {
        padding-bottom: 0px;
    }
    .info-label {
        font-weight: 600;
    }
    .cv-card {
        color: #588B8B;
    }
    .padding-fix {
        padding-left: 0px;
        padding-right: 0px;
    }
    .margin-fix {
        margin-left: 0px;
        margin-right: 0px;
    }
    .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
        border: 0;
        padding: 0;
    }
</style>