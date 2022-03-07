<section>
    <div class="container">
        <div class="remaining-wrap white-bg text-color-blue-1">
            <div class="block-remain">
                <span class="text-color-lght-grey">Remaining Balance</span>
                <h4>PTH {{$settings->balance}}</h4>
            </div>
            @if($loginuser->agent_level != 'DL')
            <div class="block-remain">
                <span class="text-color-lght-grey">Total Agent Balance</span>
                <h4>PTH 0.00</h4>
            </div>
             @endif
            <div class="block-remain">
                <span class="text-color-lght-grey">Total Client Balance</span>
                <h4>PTH 1844.87</h4>
            </div>            
            <div class="block-remain">
                <span class="text-color-lght-grey">Exposure</span>
                <h4>PTH <div class="text-color-red">(0.00)</div></h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">Available Balances</span>
                <h4>PTH {{$settings->balance}}</h4>
            </div>
            <div class="block-remain">
                <span class="text-color-lght-grey">Ledger Exposure</span>
                <h4>PTH <div class="text-color-green">2715.20</div></h4>
            </div>
        </div>
    </div>
</section>