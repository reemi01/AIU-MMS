@extends('layouts.admin')

@section('title', 'Manage Account')

@push('styles')
<style>
.main{flex:1;padding:22px}
.container{max-width:1000px;margin:0 auto}
.section{background:white;border-radius:12px;padding:24px;box-shadow:0 2px 8px rgba(0,0,0,0.05);margin-bottom:24px}
.section h3{margin:0 0 8px;font-size:20px;font-weight:700}
.section p{margin:0 0 20px;color:#6b7280;font-size:14px}
.row{display:flex;gap:24px;align-items:flex-start}
.col{flex:1}
.avatar-section{width:200px;text-align:center}
.bigAvatar{width:160px;height:160px;border-radius:16px;background:linear-gradient(135deg,#7c3aed,#a78bfa);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:56px;background-size:cover;background-position:center;margin:0 auto}
label{display:block;margin-top:16px;font-weight:600;color:#374151;font-size:14px}
.input{width:100%;padding:10px 14px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;transition:all 0.2s;font-size:14px;margin-top:6px}
.input:focus{outline:none;border-color:#7c3aed;box-shadow:0 0 0 3px rgba(124,58,237,0.1)}
.error-msg{color:#ef4444;font-size:13px;margin-top:4px}
.btn{background:#7c3aed;color:#fff;border:0;padding:10px 20px;border-radius:8px;cursor:pointer;font-weight:600;transition:all 0.2s}
.btn:hover{background:#6d28d9;transform:translateY(-1px)}
.btn-secondary{background:#e5e7eb;color:#111}
.btn-secondary:hover{background:#d1d5db}
.btn-small{padding:8px 16px;font-size:13px;margin-top:12px}
.divider{height:1px;background:#e5e7eb;margin:24px 0}
@media (max-width:768px){
  .main{padding:12px}
  .section{padding:16px}
  .row{flex-direction:column}
  .avatar-section{width:100%}
}
</style>
@endpush

@section('content')
<main class="main">
  <div class="container">
    <h1 style="font-size:28px;font-weight:800;margin:0 0 24px">Manage Account</h1>

    <!-- Profile Information Section -->
    <div class="section">
      <h3>Profile Information</h3>
      <p>Update your account's profile information</p>

      <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm">
        @csrf
        @method('PATCH')
        <input type="hidden" name="avatar" id="avatarData" value="{{ $user->avatar ?? '' }}">

        <div class="row">
          <div class="avatar-section">
            @if($user->avatar)
              <div id="avatarPreview" class="bigAvatar" style="background-image:url({{ $user->avatar }})"></div>
            @else
              <div id="avatarPreview" class="bigAvatar">{{ substr($user->name, 0, 1) }}</div>
            @endif
            <input id="avatarInput" type="file" accept="image/*" style="display:none" />
            <button type="button" id="changePhoto" class="btn btn-small">Change Photo</button>
          </div>

          <div class="col">
            <label for="name">Full Name <span style="color:#ef4444">*</span></label>
            <input id="name" name="name" type="text" class="input @error('name') error-input @enderror" value="{{ old('name', $user->name) }}" required />
            @error('name')
            <div class="error-msg">{{ $message }}</div>
            @enderror

            <label for="username">Username <span style="color:#ef4444">*</span></label>
            <input id="username" name="username" type="text" class="input @error('username') error-input @enderror" value="{{ old('username', $user->username) }}" required />
            @error('username')
            <div class="error-msg">{{ $message }}</div>
            @enderror

            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="input @error('email') error-input @enderror" value="{{ old('email', $user->email) }}" />
            @error('email')
            <div class="error-msg">{{ $message }}</div>
            @enderror

            <div style="margin-top:20px;display:flex;gap:8px">
              <button type="submit" class="btn">Save Changes</button>
              <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Change Password Section -->
    <div class="section">
      <h3>Change Password</h3>
      <p>Ensure your account is using a secure password</p>

      <form action="{{ route('admin.profile.update-password') }}" method="POST">
        @csrf
        @method('PATCH')

        <div style="max-width:500px">
          <label for="current_password">Current Password <span style="color:#ef4444">*</span></label>
          <input id="current_password" name="current_password" type="password" class="input @error('current_password') error-input @enderror" required />
          @error('current_password')
          <div class="error-msg">{{ $message }}</div>
          @enderror

          <label for="new_password">New Password <span style="color:#ef4444">*</span></label>
          <input id="new_password" name="new_password" type="password" class="input @error('new_password') error-input @enderror" required />
          @error('new_password')
          <div class="error-msg">{{ $message }}</div>
          @enderror
          <div style="font-size:12px;color:#6b7280;margin-top:4px">Minimum 6 characters</div>

          <label for="new_password_confirmation">Confirm New Password <span style="color:#ef4444">*</span></label>
          <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="input" required />

          <div style="margin-top:20px">
            <button type="submit" class="btn">Update Password</button>
          </div>
        </div>
      </form>
    </div>

    <!-- Account Information -->
    <div class="section">
      <h3>Account Information</h3>
      <p>Details about your account</p>

      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:16px">
        <div>
          <div style="font-size:13px;color:#6b7280">Role</div>
          <div style="font-weight:600;margin-top:4px">{{ ucfirst($user->role) }}</div>
        </div>
        <div>
          <div style="font-size:13px;color:#6b7280">Account Created</div>
          <div style="font-weight:600;margin-top:4px">{{ $user->created_at->format('M d, Y') }}</div>
        </div>
        <div>
          <div style="font-size:13px;color:#6b7280">Last Updated</div>
          <div style="font-weight:600;margin-top:4px">{{ $user->updated_at->format('M d, Y H:i') }}</div>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
document.getElementById('changePhoto').addEventListener('click', function() {
  document.getElementById('avatarInput').click();
});

document.getElementById('avatarInput').addEventListener('change', function(e) {
  const file = e.target.files && e.target.files[0];
  if (!file) return;

  // Check file size (max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    alert('File size must be less than 2MB');
    return;
  }

  const reader = new FileReader();
  reader.onload = function(event) {
    const preview = document.getElementById('avatarPreview');
    preview.style.backgroundImage = `url(${event.target.result})`;
    preview.textContent = '';
    document.getElementById('avatarData').value = event.target.result;
  };
  reader.readAsDataURL(file);
});
</script>
@endsection
