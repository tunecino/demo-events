import React from "react";

interface PaymentModalProps {
  show: boolean;
  onPay: () => void;
  onCancel: () => void;
  isLoading: boolean;
  price: string;
}

const PaymentModal: React.FC<PaymentModalProps> = ({
  show,
  onPay,
  onCancel,
  isLoading,
  price,
}) => {
  if (!show) return null;

  return (
    <>
      <input
        type="checkbox"
        id="payment-modal"
        className="modal-toggle"
        checked={show}
        readOnly
      />
      <div className="modal">
        <div className="modal-box">
          <h3 className="font-bold text-lg">Confirm Payment</h3>
          <p className="py-4">Book this slot for {price}?</p>
          <div className="modal-action">
            <button
              className="btn btn-primary"
              onClick={onPay}
              disabled={isLoading}
            >
              {isLoading ? (
                <span className="loading loading-spinner"></span>
              ) : (
                "Pay Now"
              )}
            </button>
            <button className="btn btn-ghost" onClick={onCancel}>
              Cancel
            </button>
          </div>
        </div>
      </div>
    </>
  );
};

export default PaymentModal;
