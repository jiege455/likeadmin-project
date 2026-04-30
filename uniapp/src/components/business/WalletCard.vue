<template>
    <div class="wallet-card">
        <div class="card-content">
            <div class="balance-section">
                <div class="label">当前余额 (元)</div>
                <div class="amount">{{ balance }}</div>
            </div>
            <div class="income-section">
                <van-cell title="累计收入" :value="'¥ ' + income" is-link />
            </div>
            <div class="actions">
                <van-button
                    size="small"
                    type="primary"
                    class="action-btn"
                    @click="$emit('withdraw')"
                    >提现</van-button
                >
                <van-button
                    size="small"
                    plain
                    type="primary"
                    class="action-btn"
                    @click="$emit('recharge')"
                    >充值</van-button
                >
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
/**
 * WalletCard 钱包余额卡片
 *
 * Props:
 * - balance: 余额
 * - income: 累计收入
 *
 * Events:
 * - withdraw: 提现点击
 * - recharge: 充值点击
 */
defineProps<{
    balance: number | string
    income: number | string
}>()

defineEmits(['withdraw', 'recharge'])
</script>

<style scoped>
.wallet-card {
    padding: 16px;
}

.card-content {
    background: linear-gradient(135deg, #1989fa, #4a9bf8);
    border-radius: 12px;
    color: #fff;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(25, 137, 250, 0.3);
}

.balance-section {
    text-align: center;
    margin-bottom: 20px;
}

.balance-section .label {
    font-size: 14px;
    opacity: 0.8;
    margin-bottom: 8px;
}

.balance-section .amount {
    font-size: 32px;
    font-weight: bold;
}

.income-section :deep(.van-cell) {
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: #fff;
}

.income-section :deep(.van-cell__title),
.income-section :deep(.van-cell__value) {
    color: #fff;
}

.income-section :deep(.van-icon) {
    color: #fff;
}

.actions {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.action-btn {
    width: 45%;
    border-radius: 6px;
}
</style>
