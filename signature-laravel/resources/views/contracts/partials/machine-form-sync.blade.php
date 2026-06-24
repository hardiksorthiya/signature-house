@once('contract-machine-form-sync')
@push('scripts')
<script>
    /**
     * Sync Alpine machine state into hidden inputs before POST.
     * Safari/Firefox can omit fields rendered via x-for/x-if inside forms.
     */
    function syncContractMachineFields(form, machines) {
        form.querySelectorAll('[data-machine-sync]').forEach(function(el) { el.remove(); });

        machines.forEach(function(machine, index) {
            var fields = {
                machine_category_id: machine.machine_category_id,
                brand_id: machine.brand_id,
                machine_model_id: machine.machine_model_id,
                seller_id: machine.seller_id,
                quantity: machine.quantity,
                amount: machine.amount,
                description: machine.description,
                machine_size_id: machine.machine_size_id,
                feeder_id: machine.feeder_id,
                machine_hook_id: machine.machine_hook_id,
                machine_e_read_id: machine.machine_e_read_id,
                color_id: machine.color_id,
                machine_nozzle_id: machine.machine_nozzle_id,
                machine_dropin_id: machine.machine_dropin_id,
                machine_beam_id: machine.machine_beam_id,
                machine_cloth_roller_id: machine.machine_cloth_roller_id,
                machine_software_id: machine.machine_software_id,
                hsn_code_id: machine.hsn_code_id,
                wir_id: machine.wir_id,
                machine_shaft_id: machine.machine_shaft_id,
                machine_lever_id: machine.machine_lever_id,
                machine_chain_id: machine.machine_chain_id,
                machine_heald_wire_id: machine.machine_heald_wire_id
            };

            for (var key in fields) {
                if (!Object.prototype.hasOwnProperty.call(fields, key)) continue;
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'machines[' + index + '][' + key + ']';
                input.value = fields[key] != null && fields[key] !== '' ? String(fields[key]) : '';
                input.setAttribute('data-machine-sync', '1');
                form.appendChild(input);
            }
        });
    }
</script>
@endpush
@endonce
