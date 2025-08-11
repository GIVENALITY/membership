<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Register - Membership MS</title>
    <meta name="description" content="" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    
    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ route('register') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <defs>
                                            <path d="M13.7918663,0.358365126 L3.39788168,7.44144159 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.933988585,16.7498146 1.87959007,17.5583862 2.93367371,17.9579848 C3.94419256,18.363545 4.56108108,19.2604198 4.89889341,19.6833294 C5.17669052,20.0142672 5.52285305,20.1201347 5.88091883,20.1032674 C6.65569584,20.0596379 7.41969943,19.8239519 8.11996507,19.4033049 L21.856,10.4976938 C23.9021832,9.38244078 25.0517663,7.02571992 25.0517663,4.47333537 C25.0517663,1.92195082 23.9021832,-0.434770042 21.856,-1.55002105 L8.11996507,-10.4976938 C7.41969943,-10.9183408 6.65569584,-11.1540268 5.88091883,-11.1976563 C5.52285305,-11.2145236 5.17669052,-11.1086561 4.89889341,-10.7777183 C4.56108108,-10.3448087 3.94419256,-9.44793395 2.93367371,-9.04237373 C1.87959007,-8.64277513 0.933988585,-7.83420357 0.557900856,-6.88044412 C-0.379795268,-3.56324865 0.566865006,-0.778477859 3.39788168,1.47458846 L13.7918663,0.358365126 Z" id="path-1"></path>
                                            <path d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 L13.7918663,0.358365126 C13.7918663,0.358365126 13.7918663,0.358365126 13.7918663,0.358365126 C13.7918663,0.358365126 13.7918663,0.358365126 13.7918663,0.358365126 Z" id="path-3"></path>
                                            <path d="M7.50063644,21.2294429 L12.3234468,23.3556932 C14.9258025,24.7570539 16.9123572,25.4673244 18.1105607,25.5470818 C19.3101129,25.6269373 20.0412455,25.4108611 20.4548293,24.9841849 C20.6709685,24.7214036 20.7178111,24.3919432 20.5953845,24.0311962 L19.5393377,20.8687242 C19.2819466,20.1316222 18.7060819,19.5857516 17.9695559,19.4005674 C17.2330299,19.2153832 16.4371504,19.4053785 15.9034477,19.9032525 L13.6106297,21.2171113 L7.50063644,21.2294429 Z" id="path-4"></path>
                                            <path d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17.0070343 24,17 C23.5672596,17.0070343 23.1461923,16.8596443 22.8,16.6 L18.6,13.1333333 L20.6,7.13333333 Z" id="path-5"></path>
                                        </defs>
                                        <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                                                <g id="Icon" transform="translate(27.000000, 15.000000)">
                                                    <g id="Mask" transform="translate(0.000000, 8.000000)">
                                                        <mask id="mask-2" fill="white">
                                                            <use xlink:href="#path-1"></use>
                                                        </mask>
                                                        <use fill="#696cff" xlink:href="#path-1"></use>
                                                        <g id="Path-3" mask="url(#mask-2)">
                                                            <use fill="#696cff" xlink:href="#path-3"></use>
                                                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                                        </g>
                                                        <g id="Path-4" mask="url(#mask-2)">
                                                            <use fill="#696cff" xlink:href="#path-4"></use>
                                                            <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                                        </g>
                                                    </g>
                                                    <g id="Triangle" transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                                        <use fill="#696cff" xlink:href="#path-5"></use>
                                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                                    </g>
                                                </g>
                                            </g>
                                        </g>
                                    </svg>
                                </span>
                                <span class="app-brand-text demo text-heading fw-bolder">Membership MS</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Adventure starts here 🚀</h4>
                        <p class="mb-4">Register your hotel and start managing your membership program!</p>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <!-- Hotel Information -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Hotel Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_name" class="form-label">Hotel Name *</label>
                                            <input type="text" class="form-control" id="hotel_name" name="hotel_name" placeholder="Enter hotel name" value="{{ old('hotel_name') }}" required />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_email" class="form-label">Hotel Email *</label>
                                            <input type="email" class="form-control" id="hotel_email" name="hotel_email" placeholder="Enter hotel email" value="{{ old('hotel_email') }}" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_phone" class="form-label">Hotel Phone</label>
                                            <input type="text" class="form-control" id="hotel_phone" name="hotel_phone" placeholder="Enter hotel phone" value="{{ old('hotel_phone') }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_website" class="form-label">Hotel Website</label>
                                            <input type="url" class="form-control" id="hotel_website" name="hotel_website" placeholder="https://example.com" value="{{ old('hotel_website') }}" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="hotel_city" name="hotel_city" placeholder="Enter city" value="{{ old('hotel_city') }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_country" class="form-label">Country</label>
                                            <input type="text" class="form-control" id="hotel_country" name="hotel_country" placeholder="Enter country" value="{{ old('hotel_country', 'Tanzania') }}" />
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="hotel_address" class="form-label">Address</label>
                                        <textarea class="form-control" id="hotel_address" name="hotel_address" rows="2" placeholder="Enter hotel address">{{ old('hotel_address') }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="hotel_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="hotel_description" name="hotel_description" rows="3" placeholder="Enter hotel description">{{ old('hotel_description') }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_logo" class="form-label">Hotel Logo</label>
                                            <input type="file" class="form-control" id="hotel_logo" name="hotel_logo" accept="image/*" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="hotel_banner" class="form-label">Hotel Banner</label>
                                            <input type="file" class="form-control" id="hotel_banner" name="hotel_banner" accept="image/*" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin User Information -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Admin Account</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_name" class="form-label">Admin Name *</label>
                                            <input type="text" class="form-control" id="admin_name" name="admin_name" placeholder="Enter admin name" value="{{ old('admin_name') }}" required />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_email" class="form-label">Admin Email *</label>
                                            <input type="email" class="form-control" id="admin_email" name="admin_email" placeholder="Enter admin email" value="{{ old('admin_email') }}" required />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_password" class="form-label">Password *</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" id="admin_password" class="form-control" name="admin_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="admin_password_confirmation" class="form-label">Confirm Password *</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" id="admin_password_confirmation" class="form-control" name="admin_password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required />
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="javascript:void(0);">privacy policy & terms</a>
                                    </label>
                                </div>
                            </div>
                            <button class="btn btn-primary d-grid w-100" type="submit">Create Account</button>
                        </form>

                        <p class="text-center">
                            <span>Already have an account?</span>
                            <a href="{{ route('login') }}">
                                <span>Sign in instead</span>
                            </a>
                        </p>

                        <div class="divider my-4">
                            <div class="divider-text">or</div>
                        </div>

                        <div class="d-flex justify-content-center">
                            <a href="javascript:;" class="btn btn-icon btn-label-facebook me-3">
                                <i class="tf-icons bx bxl-facebook"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon btn-label-google-plus me-3">
                                <i class="tf-icons bx bxl-google"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon btn-label-twitter">
                                <i class="tf-icons bx bxl-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    
    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    
    <!-- Page JS -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html> 