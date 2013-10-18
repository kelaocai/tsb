//
//  PostListTableCell.m
//  ipapp
//
//  Created by kelaocai on 13-10-16.
//  Copyright (c) 2013年 tongshibang. All rights reserved.
//

#import "PostListTableCell.h"
#import <QuartzCore/QuartzCore.h>
@implementation PostListTableCell



- (id)initWithStyle:(UITableViewCellStyle)style reuseIdentifier:(NSString *)reuseIdentifier
{
    self = [super initWithStyle:style reuseIdentifier:reuseIdentifier];
    if (self) {
        // Initialization code
        self.contentView.backgroundColor=[UIColor grayColor];
        
        self.avatar = [[[UIImageView alloc] initWithFrame:CGRectMake(15, 5, 36, 36)] autorelease];
        self.avatar.backgroundColor = [UIColor clearColor];
        //设置圆角
        self.avatar.layer.cornerRadius = 8;
        self.avatar.layer.masksToBounds = YES;
        //自适应图片宽高比例
        self.avatar.contentMode = UIViewContentModeScaleAspectFit;
        [self.contentView addSubview:self.avatar];
        self.message=[[UILabel alloc] initWithFrame:CGRectMake(85, 5, 450,20)];
        self.message.numberOfLines=0;
        self.message.font=[UIFont systemFontOfSize:14.0f];
        self.message.textAlignment=NSTextAlignmentLeft;
        //文字背景
        UIEdgeInsets insets=UIEdgeInsetsMake(8.0f, 8.0f, 8.0f, 8.0f);
        UIImage *msg_bg_img=[[UIImage imageNamed:@"msg_bg"] resizableImageWithCapInsets:insets resizingMode:UIImageResizingModeStretch];
        self.messageBgView=[[[UIImageView alloc] initWithImage:msg_bg_img] autorelease];
        self.messageBgView.frame=CGRectMake(80, 5, 450, 200);
        self.messageBgView.contentMode = UIViewContentModeScaleToFill;
        self.messageBgView.alpha=0.8f;
        [self.contentView addSubview:self.messageBgView];
        self.message.backgroundColor=[UIColor clearColor];
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
