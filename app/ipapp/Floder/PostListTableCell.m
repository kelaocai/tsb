//
//  PostListTableCell.m
//  ipapp
//
//  Created by kelaocai on 13-10-16.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "PostListTableCell.h"

@implementation PostListTableCell



- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
        
        self.avatar = [[[UIImageView alloc] initWithFrame:CGRectMake(5, 5, 70, 70)] autorelease];
        self.avatar.backgroundColor = [UIColor clearColor];
        [self.contentView addSubview:self.avatar];
        self.message=[[UILabel alloc] initWithFrame:CGRectMake(85, 5, 450,20)];
        self.message.numberOfLines=0;
        self.message.textAlignment=NSTextAlignmentLeft;
        self.message.lineBreakMode=NSLineBreakByWordWrapping;
        [self.contentView addSubview:self.message];
        
        
        
    }
    return self;
}

- (void)setSelected:(BOOL)selected animated:(BOOL)animated
{
    [super setSelected:selected animated:animated];

    // Configure the view for the selected state
}

@end
