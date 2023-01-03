@extends ('layouts.app')

@section('title', 'Contact Us')

@section('content')

<div class="formbg-outer">
        <div class="formbg">
            <div class="formbg-inner" style="padding: 48px">
            <h5 style="padding-bottom: 0.5em">Get in touch</h5>
                <form id="stripe-login" method="POST" action="contact_us">
                    {{ csrf_field() }}
                    <div class="field" style="padding-bottom: 24px">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required>
                    </div>
                    @if ($errors->has('name'))
                        <div class="field">
                            <span class="error">
                                {{ $errors->first('name') }}
                            </span>
                        </div>
                    @endif
                    <div class="field" style="padding-bottom: 24px">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required>
                    </div>
                    @if ($errors->has('email'))
                        <div class="field">
                            <span class="error">
                                {{ $errors->first('email') }}
                            </span>
                        </div>
                    @endif
                    <div class="field" style="padding-bottom: 24px">
                        <label>Message</label>
                        <textarea class="form-control" rows="3" name="message" id="message" required>{{ old('message') }}</textarea>
                    </div>
                    @if ($errors->has('message'))
                        <div class="field">
                            <span class="error">
                                {{ $errors->first('message') }}
                            </span>
                        </div>
                    @endif
                    <div class="field" style="padding-bottom: 24px">
                        <input id="submit" type="submit" name="submit" value="contact us">
                    </div>
                </form>
            </div>
        </div>

    
</div>
@endsection