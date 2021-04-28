<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel | Braintree</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            input{
                width: 100%;
                padding: 12px 20px;
                margin: 8px 0;
                box-sizing: border-box;
                border: 1px solid #ddd;
                }
            .button {
            width: 100%;
            background-color: #EF3B2D;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 25px 0px;
            }
            .button:hover {
            background-color: #d1352a;
            transition: background-color 0.3s ease;
            }

            .button-sec{
                background-color: #424242 !important;
            }

            .button-sec:hover{
                background-color: #2c2c2c !important;
                transition: background-color 0.3s ease;
            }

            .text-red{
                color: #EF3B2D;
                /* background-color: #d1352a; */
            }

            .successMessage{
                /* width: 100%; */
                padding: 10px;
                color: #0e3d0f;
                font-weight: bold;
                background-color: rgba(76, 175, 80,0.5);
            }

            .errorMessage{
                padding: 10px;
                color: white;
                font-weight: bold;
                background-color:rgba(244, 67, 54,0.5);
            }

            .errorMessage div{
                padding: 10px;
                margin: 10px;
                color: inherit;
                font-weight: bold;
                background-color:rgba(244, 67, 54,0.5);

            }

            .subs-card {
                background-color: #fff;
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                transition: 0.3s;
                min-width: 350px;
                border-radius: 10px;
                margin: 20px;
            }

            .subs-card:hover {
                box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
            }

            .subs-card-container {
                padding: 20px;
                /* text-align: center; */
            }

            #selectPlan{
                flex-flow: column;
            }

            .center{
                text-align: center;
            }

            .backButton{
                padding: 3px 10px;
                border: 2px solid #EF3B2D;
                border-radius: 10px;
                font-size: 18px;
                color: #EF3B2D;
            }

            .backButton:hover{
                background-color: #EF3B2D;
                padding: 3px 10px;
                border-radius: 10px;
                font-size: 18px;
                color: #e2e8f0;
                transition: all 0.2s ease;
            }

            
            /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}}
        </style>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>
    </head>
    <body class="antialiased">
         
        <div class="relative flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900" id="selectPlan">

             <h2 class="text-red"><a href="{{ url("/")}}"><span class="backButton">🡸</span></a> Manage Subscriptions</h2>

            <div class="relative flex items-top justify-center sm:items-center py-4 sm:pt-0" style="flex-flow: row wrap">
                @foreach ($customer_plans as $plan)
                    
                <div class="subs-card">
                        <div class="subs-card-container center">
                            <h2 class="center">{{ $plan_names[$plan->planId] }}</h2>
                            <h2 class="text-red center">{{ $plan->price }}</h2>
                            <p>Status:<b> {{ $plan->status }}</b></p>
                            <p>Subscribed At:<b> {{ $plan->createdAt->format('Y-m-d') }}</b></p>
                            @if ($plan->status != "Canceled")
                                
                                
                                @if (true)
                                    <p>Trial Ends on: <b>{{ $plan->firstBillingDate->format('Y-m-d H:i') }}</b></p>
                                @else
                                    <p>Trial:<b> Ended</b></p>
                                    <p>Next Billing Date:<b> {{ $plan->nextBillingDate->format('Y-m-d') }}</b></p>      
                                @endif
                                <form action="{{ url("/subscription/cancel")}}" method="POST">
                                    @csrf
                                    @method('delete')
                                    <input type="hidden" name="subscription_id" value="{{$plan->id}}">
                                    <button class="button" type="submit"><span>Cancel</span></button>
                                </form>
                            @endif
                        </div>
                </div>
                @endforeach
                
            </div>

            {{-- <h2 class="text-red">Canceled Subscriptions</h2> --}}


    </body>
    
</html>
