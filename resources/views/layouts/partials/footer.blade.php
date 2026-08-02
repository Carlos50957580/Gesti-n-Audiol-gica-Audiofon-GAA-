<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © {{ company('name', 'Audiofon') }}
                @if(company('rnc'))
                    <span class="text-muted" style="font-size:12px;margin-left:8px;">
                        RNC: {{ company('rnc') }}
                    </span>
                @endif
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    {{ company('footer_text', 'Desarrollado con ') }}<i class="mdi mdi-heart text-danger"></i>
                    @if(company('phone'))
                        <span class="text-muted" style="font-size:12px;margin-left:8px;">
                            <i class="mdi mdi-phone"></i> {{ company('phone') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12 text-center text-muted" style="font-size:11px;">
                {{ company('business_name') }} 
                @if(company('address'))
                    · {{ company('address') }}
                @endif
                @if(company('email'))
                    · {{ company('email') }}
                @endif
            </div>
        </div>
    </div>
</footer>