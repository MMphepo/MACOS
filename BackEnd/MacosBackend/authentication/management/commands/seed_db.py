from django.core.management.base import BaseCommand
from django.utils import timezone
from authentication.models import Users, Consumer, MacraStaff, ServiceProvider, ComplaintCategory, ComplaintStatus, Complaint
import random
from faker import Faker

class Command(BaseCommand):
    help = 'Seed the database with test data: 10 consumers, 1 Macra Staff per job title, and random complaints.'

    def handle(self, *args, **kwargs):
        fake = Faker()


        # Use provided MACRA staff roles
        job_titles = [
            'Director General',
            'Consumer Affairs Manager',
            'Senior Consumer Affairs Officer',
            'Investigation Officer',
            'Consumer Affairs Officer',
            'Consumer Affairs Assistant',
            'Legal Officer',
            'Data Entry Clerk',
            'Registry Clerk',
            'Administrative Assistant',
            'Front Office Clerk/Receptionist',
        ]

        # Use provided service provider names
        provider_names = ['TNM', 'Airtel', 'MTL', 'Access', 'Malawi Digital']
        providers = []
        for name in provider_names:
            provider = ServiceProvider.objects.create(
                provider_name=name,
                contact_person=fake.name(),
                contact_email=fake.email(),
                contact_phone=fake.phone_number(),
                license_number=fake.unique.bothify(text='LIC####'),
            )
            providers.append(provider)

        # Create Complaint Categories
        categories = []
        for name in ['Billing', 'Service Quality', 'Network', 'Customer Care', 'Other']:
            category, _ = ComplaintCategory.objects.get_or_create(category_name=name)
            categories.append(category)

        # Create Complaint Statuses
        statuses = []
        for name in ['Pending', 'In Progress', 'Resolved', 'Closed']:
            status, _ = ComplaintStatus.objects.get_or_create(status_name=name)
            statuses.append(status)

        # Create 10 Consumers
        consumers = []
        for i in range(10):
            user = Users.objects.create_user(
                username=fake.unique.user_name(),
                email=fake.unique.email(),
                password='password123',
                first_name=fake.first_name(),
                last_name=fake.last_name(),
                role='consumer',
            )
            consumer = Consumer.objects.create(
                user=user,
                phone_number=fake.phone_number(),
                address=fake.address(),
            )
            consumers.append(consumer)

        # Create 1 Macra Staff for each job title
        for job_title in job_titles:
            user = Users.objects.create_user(
                username=fake.unique.user_name(),
                email=fake.unique.email(),
                password='password123',
                first_name=fake.first_name(),
                last_name=fake.last_name(),
                role='macra_staff',
            )
            MacraStaff.objects.create(
                user=user,
                phone_number=fake.phone_number(),
                job_title=job_title,
                department=fake.word(),
                hire_date=fake.date_between(start_date='-10y', end_date='today'),
            )

        # Create random complaints for each consumer
        for consumer in consumers:
            num_complaints = random.randint(1, 5)
            for _ in range(num_complaints):
                Complaint.objects.create(
                    consumer=consumer,
                    provider=random.choice(providers),
                    category=random.choice(categories),
                    status=random.choice(statuses),
                    complaint_details=fake.text(max_nb_chars=200),
                    complaint_date=timezone.now(),
                )

        self.stdout.write(self.style.SUCCESS('Database seeded successfully!'))
