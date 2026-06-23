import re

# Fix PO create.blade.php
po_file = r"d:\SMT 4\Project sql\resources\views\purchase-orders\create.blade.php"
with open(po_file, 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Find and replace first @push('scripts') and corresponding @endpush
output = []
skip_until_endpush = False
replaced_first = False

for i, line in enumerate(lines):
    # Skip @push('scripts') until @endpush (approximately lines 5-66)
    if i == 4 and "@push('scripts')" in line and not replaced_first:  # Line 5 is index 4
        skip_until_endpush = True
        replaced_first = True
        output.append("@section('extra-js')\n")
        continue
    
    if skip_until_endpush and "@endpush" in line and i < 70:
        skip_until_endpush = False
        continue
    
    output.append(line)

# Now fix @push('extra-js') to @section('extra-js')
content = ''.join(output)
content = content.replace("@push('extra-js')", "@section('extra-js')")
content = content.rstrip() + '\n@endsection\n'

with open(po_file, 'w', encoding='utf-8') as f:
    f.write(content)
print("PO create.blade.php fixed")

# Fix Receiving create.blade.php
recv_file = r"d:\SMT 4\Project sql\resources\views\receivings\create.blade.php"
with open(recv_file, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace @push('scripts') with @section('extra-js')
content = content.replace("@push('scripts')", "@section('extra-js')")
content = content.replace("\n@endpush", "\n@endsection")

with open(recv_file, 'w', encoding='utf-8') as f:
    f.write(content)
print("Receiving create.blade.php fixed")

print("All files fixed!")
