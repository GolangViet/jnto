<?php $current = $user ?? []; ?>
<label>Username</label>
<input type="text" name="username" value="<?= e((string) old('username', $current['username'] ?? '')) ?>" required>

<label>Name</label>
<input type="text" name="name" value="<?= e((string) old('name', $current['name'] ?? '')) ?>" required>

<label>Email</label>
<input type="email" name="email" value="<?= e((string) old('email', $current['email'] ?? '')) ?>" required>

<label>Role</label>
<select name="role">
    <option value="user" <?= old('role', $current['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
    <option value="admin" <?= old('role', $current['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
</select>

<label>Password <?= !empty($current) ? '<span class="muted">(Leave blank to keep unchanged)</span>' : '' ?></label>
<input type="password" name="password" <?= empty($current) ? 'required' : '' ?>>

<label>Confirm Password</label>
<input type="password" name="password_confirmation" <?= empty($current) ? 'required' : '' ?>>
