// async function confirmCardPayment(clientKey) {
//     try {
//         const card = {
//             number: '4242424242424242',   // Test card number
//             exp_month: '12',
//             exp_year: '2030',
//             cvc: '123'
//         };

//         const result = await PayMongo.confirmCardPayment(clientKey, { card });

//         if (result.status === 'succeeded') {
//             alert('Payment successful!');
//         } else {
//             console.log(result);
//             alert('Payment not completed: ' + result.status);
//         }
//     } catch (error) {
//         console.error(error);
//         alert('Error confirming payment: ' + error.message);
//     }
// }


// const overlay = document.getElementById('overlay');
// const cartModal = document.getElementById('cartModal');
// const checkoutModal = document.getElementById('checkoutModal');

// // Update cart summary
// function updateCartSummary() {
//     const items = document.querySelectorAll('.cart-item');
//     let total = 0;
//     let count = 0;

//     items.forEach(item => {
//         const checkbox = item.querySelector('.item-select');
//         if (checkbox && checkbox.checked) {
//             const priceText = item.querySelector('.item-price').textContent.replace('$', '');
//             const quantity = parseInt(item.querySelector('.quantity-value').textContent);
//             total += parseFloat(priceText) * quantity;
//             count += quantity;
//         }
//     });

//     document.getElementById('selectedCount').textContent = count;
//     document.getElementById('selectedTotal').textContent = $${ total.toFixed(2) };
// }

// // Checkbox listener
// document.addEventListener('change', (e) => {
//     if (e.target.classList.contains('item-select')) {
//         updateCartSummary();
//     }
// });

// // Open / Close Modals
// function openCart() {
//     overlay.classList.add('show');
//     cartModal.classList.add('show');
//     checkoutModal.classList.remove('show');
// }
// function openCheckout() {
//     overlay.classList.add('show');
//     checkoutModal.classList.add('show');
//     cartModal.classList.remove('show');
// }
// function closeModals() {
//     overlay.classList.remove('show');
//     cartModal.classList.remove('show');
//     checkoutModal.classList.remove('show');
// }
// function proceedToCheckout() {
//     cartModal.classList.remove('show');
//     checkoutModal.classList.add('show');
// }

// async function placeOrder() {
//     const totalText = document.querySelector(".total-value").textContent.replace("$", "");
//     const totalAmount = parseFloat(totalText);

//     const paymentType = document.querySelector('input[name="payment"]:checked').value;

//     try {
//         const response = await fetch('index.php?api=paymongo', {
//             method: 'POST',
//             headers: { 'Content-Type': 'application/json' },
//             body: JSON.stringify({ amount: totalAmount })
//         });

//         const data = await response.json();

//         if (data.client_key) {
//             // Confirm card payment using PayMongo JS SDK
//             confirmCardPayment(data.client_key);
//         }

//         if (!data.client_key) {
//             alert("Failed to create payment. Please try again.");
//             return;
//         }


//         if (paymentType === 'gcash') {
//             if (data.redirect) {
//                 window.location.href = data.redirect;
//             } else {
//                 alert("GCash payment is ready. Open PayMongo dashboard to complete.");
//             }
//         } else if (paymentType === 'card') {
//             // Card payment confirmation
//             const card = {
//                 number: document.getElementById('card-number').value,
//                 exp_month: document.getElementById('card-exp-month').value,
//                 exp_year: document.getElementById('card-exp-year').value,
//                 cvc: document.getElementById('card-cvc').value
//             };

//             // Basic validation
//             if (!card.number || !card.exp_month || !card.exp_year || !card.cvc) {
//                 alert("Please fill all card fields.");
//                 return;
//             }

//             // Use PayMongo JS SDK to confirm card payment
//             const result = await PayMongo.confirmCardPayment(data.client_key, { card });

//             if (result.status === 'succeeded') {
//                 alert("Payment successful!");
//                 closeModals();
//                 // Optionally redirect or clear cart here
//             } else {
//                 alert("Payment failed: " + JSON.stringify(result));
//             }
//         }

//     } catch (error) {
//         console.error(error);
//         alert("Error processing payment. Please try again.");
//     }
// }


// // Close modal on ESC key
// document.addEventListener('keydown', (e) => {
//     if (e.key === 'Escape') closeModals();
// });